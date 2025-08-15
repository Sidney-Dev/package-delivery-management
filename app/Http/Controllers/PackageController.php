<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Http\Requests\StorePackageRequest;
use App\Http\Requests\UpdatePackageRequest;
use Illuminate\Http\JsonResponse;

class PackageController extends Controller
{
    /**
     * Display a listing of packages.
     */
    public function index(): JsonResponse
    {
        $packages = Package::with(['delivery'])
            ->latest('id')
            ->paginate(15);

        return response()->json($packages);
    }

    /**
     * Store a newly created package in storage.
     */
    public function store(StorePackageRequest $request): JsonResponse
    {
        $package = Package::create($request->validated());

        return response()->json([
            'message' => 'Package created successfully.',
            'data'    => $package
        ], 201);
    }

    /**
     * Display the specified package.
     */
    public function show(Package $package): JsonResponse
    {
        $package->load(['delivery']);

        return response()->json($package);
    }

    /**
     * Update the specified package in storage.
     */
    public function update(UpdatePackageRequest $request, Package $package): JsonResponse
    {
        $package->update($request->validated());

        return response()->json([
            'message' => 'Package updated successfully.',
            'data'    => $package
        ]);
    }

    /**
     * Remove the specified package from storage.
     */
    public function destroy(Package $package): JsonResponse
    {
        $package->delete();

        return response()->json([
            'message' => 'Package deleted successfully.'
        ]);
    }
}
