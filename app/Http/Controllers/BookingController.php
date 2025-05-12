<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Booking;
use App\Models\Pricing;
use App\Models\Resource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BookingController extends Controller
{
    public function index()
    {
        return Inertia::render('Booking', [
            'resources' => Resource::where('status', 'available')->get(),
            'pricings' => Pricing::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'resource_id' => 'required|exists:resources,id',
            'start_time' => 'required|date|after:now',
            'duration' => 'required|integer|in:30,60,180,300,720',
            'package_type' => 'required|in:standard,day,night',
        ]);

        $resource = Resource::findOrFail($validated['resource_id']);
        $category = $resource->category ?? $resource->name;
        $type = $resource->type;

        $pricing = Pricing::where([
            'type' => $type,
            'category' => $category,
            'package_type' => $validated['package_type'],
            'duration' => $validated['duration'],
        ])->firstOrFail();

        $startTime = Carbon::parse($validated['start_time']);
        $endTime = $startTime->copy()->addMinutes($validated['duration']);

        $overlappingBookings = Booking::where('resource_id', $validated['resource_id'])
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($query) use ($startTime, $endTime) {
                        $query->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            })
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($overlappingBookings) {
            return redirect()->back()->withErrors(['start_time' => 'Этот ресурс уже забронирован на выбранное время.']);
        }

        DB::beginTransaction();
        try {
            $booking = Booking::create([
                'user_id' => Auth::id(),
                'resource_id' => $validated['resource_id'],
                'start_time' => $startTime,
                'end_time' => $endTime,
                'price' => $pricing->price,
            ]);

            $resource->update(['status' => 'booked']);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating resource status: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Ошибка при создании бронирования.']);
        }

        return redirect()->route('dashboard')->with('success', 'Бронирование успешно создано!');
    }

    public function cancel(Request $request, Booking $booking)
    {
        $booking->cancel();
        return redirect()->route('dashboard')->with('success', 'Бронирование отменено.');
    }

    public function extend(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'additional_duration' => 'required|integer|in:30,60,180',
        ]);

        $now = Carbon::now();
        $currentEndTime = Carbon::parse($booking->end_time);

        // Проверяем, не закончилось ли бронирование
        if ($now->greaterThanOrEqualTo($currentEndTime)) {
            return redirect()->route('dashboard')->withErrors(['error' => 'Бронирование уже завершено, его нельзя продлить.']);
        }

        $newEndTime = $currentEndTime->copy()->addMinutes($validated['additional_duration']);

        // Проверяем пересечения с другими бронированиями
        $overlappingBookings = Booking::where('resource_id', $booking->resource_id)
            ->where('id', '!=', $booking->id)
            ->where(function ($query) use ($currentEndTime, $newEndTime) {
                $query->whereBetween('start_time', [$currentEndTime, $newEndTime])
                    ->orWhereBetween('end_time', [$currentEndTime, $newEndTime])
                    ->orWhere(function ($query) use ($currentEndTime, $newEndTime) {
                        $query->where('start_time', '<=', $currentEndTime)
                            ->where('end_time', '>=', $newEndTime);
                    });
            })
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($overlappingBookings) {
            return redirect()->route('dashboard')->withErrors(['error' => 'Невозможно продлить бронирование: выбранное время пересекается с другим бронированием.']);
        }

        // Вычисляем новую стоимость
        $resource = $booking->resource;
        $category = $resource->category ?? $resource->name;
        $pricing = Pricing::where([
            'type' => $resource->type,
            'category' => $category,
            'package_type' => $request->input('package_type', 'standard'),
            'duration' => $validated['additional_duration'],
        ])->firstOrFail();

        $booking->update([
            'end_time' => $newEndTime,
            'price' => $booking->price + $pricing->price,
        ]);

        return redirect()->route('dashboard')->with('success', 'Бронирование успешно продлено!');
    }
}
