<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SellerWalletService;
use App\Repositories\SellerWalletRepository;
use Illuminate\Support\Facades\Log;

class AdminWithdrawController extends Controller
{
    protected SellerWalletService $service;
    protected SellerWalletRepository $repo;

    public function __construct(SellerWalletService $service, SellerWalletRepository $repo)
    {
        $this->middleware(['auth','role:admin']);
        $this->service = $service;
        $this->repo = $repo;
    }

    // GET /admin/withdraws
    public function index(Request $request)
    {
        $status = $request->query('status');
        $query = \App\Models\WithdrawRequest::query();
        if ($status) {
            $query->where('status', $status);
        }
        $list = $query->latest()->paginate(20);

        return response()->json(['success' => true, 'data' => $list]);
    }

    public function show($id)
    {
        $withdraw = $this->repo->findWithdrawById($id);
        if (!$withdraw) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $withdraw]);
    }

    public function approve(Request $request, $id)
    {
        try {
            $res = $this->service->adminApproveWithdraw((int)$id, auth()->id());
            return response()->json($res);
        } catch (\Exception $e) {
            Log::error('Approve withdraw error: '.$e->getMessage(), ['id' => $id]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function reject(Request $request, $id)
    {
        $data = $request->validate(['reason' => 'nullable|string']);
        try {
            $res = $this->service->adminRejectWithdraw((int)$id, auth()->id(), $data['reason'] ?? null);
            return response()->json($res);
        } catch (\Exception $e) {
            Log::error('Reject withdraw error: '.$e->getMessage(), ['id' => $id]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function complete(Request $request, $id)
    {
        try {
            $res = $this->service->adminCompleteWithdraw((int)$id, auth()->id());
            return response()->json($res);
        } catch (\Exception $e) {
            Log::error('Complete withdraw error: '.$e->getMessage(), ['id' => $id]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
