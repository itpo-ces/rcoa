<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Token;
use App\Models\Examinee;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Writer\PngWriter;

class TokenController extends Controller
{
    public function index()
    {
        $tokens = Token::all();
        $examinees = Examinee::all();
        $statuses = Token::getEnumValues('status');
        return view('admin.tokens.index', compact('tokens', 'examinees', 'statuses'));
    }

    public function postTokenData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tokenFilter' => 'nullable|string',
                'examineeFilter' => 'nullable|string',
                'statusFilter' => 'nullable|string',
                'start' => 'integer|min:0',
                'length' => 'integer|min:1|max:1000',
                'draw' => 'required|integer',
                'order' => 'nullable|array',
                'order.0.column' => 'nullable|integer',
                'order.0.dir' => 'nullable|in:asc,desc'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $validated = $validator->validated();

            $query = Token::with(['examinee']);

            // Apply filters
            if (($validated['tokenFilter'] ?? 'all') != 'all') {
                $query->where('token', $validated['tokenFilter']);
            }

            if (($validated['examineeFilter'] ?? 'all') != 'all') {
                $query->whereHas('examinee', function ($q) use ($validated) {
                    $q->where('id', $validated['examineeFilter']);
                });
            }

            if (($validated['statusFilter'] ?? 'all') != 'all') {
                $query->where('status', $validated['statusFilter']);
            }

            // Define sortable columns
            $orderableColumns = [
                'id',
                'token',
                'status',
                'examinee.id',
                'created_at',
                'updated_at'
            ];

            // Apply sorting
            if ($request->has('order') && count($request->input('order'))) {
                $order = $request->input('order.0');
                $columnIndex = $order['column'];
                
                $columnMap = [
                    0 => 'id',
                    1 => 'number',
                    2 => 'token',
                    3 => 'status',
                    4 => 'examinee.id',
                ];
                
                if (isset($columnMap[$columnIndex])) {
                    $columnName = $columnMap[$columnIndex];
                    $query->orderBy($columnName, $order['dir']);
                }
            } else {
                $query->orderBy('id', 'desc');
            }

            $totalData = $query->count();
            $start = $validated['start'] ?? 0;
            $length = $validated['length'] ?? 20;
            
            if ($length == -1) {
                $tokens = $query->skip($start)->get();
            } else {
                $tokens = $query->skip($start)->take($length)->get();
            }

            $data = $tokens->map(function ($token, $index) use ($start) {
                return [
                    'number' => $start + $index + 1,
                    'id' => $token->id,
                    'token' => $token->token,
                    'status' => $token->status_label,
                    'examinee' => $token->examinee->full_name ?? 'None',
                    'action' => $this->getActionButtons($token)
                ];
            });

            return response()->json([
                'draw' => intval($validated['draw']),
                'recordsTotal' => $totalData,
                'recordsFiltered' => $totalData,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in postTokenData: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while processing the request'
            ], 500);
        }
    }

    public function generateQRCode($tokenId)
    {
        $token = Token::findOrFail($tokenId);
        
        // Generate QR Code
        $qrCode = new QrCode(
            data: $token->token,
            size: 200,
            errorCorrectionLevel: ErrorCorrectionLevel::Low,
            margin: 20,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0, 0),
            backgroundColor: new Color(255, 255, 255, 0)
        );

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        return response($result->getString())
            ->header('Content-Type', $result->getMimeType());
    }

    public function deleteTokens(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:tokens,id'
        ]);

        try {
            Token::whereIn('id', $request->ids)->delete();
            return response()->json(['success' => true, 'message' => 'Selected tokens deleted successfully']);
        } catch (\Exception $e) {
            \Log::error('Error deleting tokens: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete tokens'], 500);
        }
    }

    private function getActionButtons($token)
    {
        return '<div class="btn-group btn-group-sm">
            <a href="' . route('tokens.qrcode', ['tokenId' => $token->id]) . '" 
                class="btn btn-outline-info qr-btn"
                data-token-id="'.$token->id.'"
                data-token="'.$token->token.'"
                title="Show QR Code">
                <i class="fas fa-qrcode"></i> QR
            </a>
            <a href="#" 
                class="btn btn-outline-danger delete-btn"
                data-token-id="'.$token->id.'"
                title="Delete Token">
                <i class="fas fa-trash"></i> Delete
            </a>
        </div>';
    }
}