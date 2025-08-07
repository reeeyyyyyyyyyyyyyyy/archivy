<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StorageRack;
use App\Models\StorageBox;
use App\Models\StorageRow;
use Illuminate\Http\Request;

class StorageController extends Controller
{
    public function racks()
    {
        $racks = StorageRack::with(['boxes', 'rows'])->get();

        return response()->json([
            'success' => true,
            'data' => $racks,
            'message' => 'Data rak berhasil diambil'
        ]);
    }

    public function rack($id)
    {
        $rack = StorageRack::with(['boxes', 'rows'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $rack,
            'message' => 'Detail rak berhasil diambil'
        ]);
    }

    public function boxes(Request $request)
    {
        $query = StorageBox::with(['rack', 'row']);

        if ($request->has('rack_id')) {
            $query->where('rack_id', $request->rack_id);
        }

        if ($request->has('row_number')) {
            $query->where('row_number', $request->row_number);
        }

        $boxes = $query->get();

        return response()->json([
            'success' => true,
            'data' => $boxes,
            'message' => 'Data box berhasil diambil'
        ]);
    }

    public function box($id)
    {
        $box = StorageBox::with(['rack', 'row'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $box,
            'message' => 'Detail box berhasil diambil'
        ]);
    }

    public function rows(Request $request)
    {
        $query = StorageRow::with(['rack']);

        if ($request->has('rack_id')) {
            $query->where('rack_id', $request->rack_id);
        }

        $rows = $query->get();

        return response()->json([
            'success' => true,
            'data' => $rows,
            'message' => 'Data row berhasil diambil'
        ]);
    }

    public function utilization()
    {
        $racks = StorageRack::with(['boxes', 'rows'])->get();

        $utilization = $racks->map(function ($rack) {
            return [
                'id' => $rack->id,
                'name' => $rack->name,
                'total_boxes' => $rack->boxes->count(),
                'available_boxes' => $rack->getAvailableBoxesCount(),
                'utilization_percentage' => $rack->getUtilizationPercentage(),
                'status' => $rack->getUtilizationPercentage() > 80 ? 'HAMPIR PENUH' : 'NORMAL'
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $utilization,
            'message' => 'Data utilisasi storage berhasil diambil'
        ]);
    }
}
