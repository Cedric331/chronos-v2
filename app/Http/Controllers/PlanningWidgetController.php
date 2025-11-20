<?php

namespace App\Http\Controllers;

use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanningWidgetController extends Controller
{
    /**
     * Update the user's planning widget preferences.
     */
    public function updatePreferences(Request $request)
    {
        $request->validate([
            'widgets' => 'nullable|array',
            'widgets.*.id' => 'required|string',
            'widgets.*.component_name' => 'required|string',
        ]);

        $user = Auth::user();
        $preferences = $user->preference;

        if (! $preferences) {
            $preferences = UserPreference::create([
                'user_id' => $user->id,
                'planning_widgets' => $request->widgets,
            ]);
        } else {
            $preferences->update([
                'planning_widgets' => $request->widgets,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Widget preferences updated successfully',
            'widgets' => $preferences->planning_widgets,
        ]);
    }

    /**
     * Update a widget's settings.
     */
    public function updateWidgetSettings(Request $request, $widgetId)
    {
        $request->validate([
            'settings' => 'required|array',
        ]);

        $user = Auth::user();
        $preferences = $user->preference;

        if (! $preferences || ! $preferences->planning_widgets) {
            return response()->json([
                'success' => false,
                'message' => 'Widget not found',
            ], 404);
        }

        $widgets = $preferences->planning_widgets;
        $widgetIndex = -1;

        // Find the widget by ID
        foreach ($widgets as $index => $widget) {
            if ($widget['id'] === $widgetId) {
                $widgetIndex = $index;
                break;
            }
        }

        if ($widgetIndex === -1) {
            return response()->json([
                'success' => false,
                'message' => 'Widget not found',
            ], 404);
        }

        // Update the widget settings
        if (! isset($widgets[$widgetIndex]['pivot'])) {
            $widgets[$widgetIndex]['pivot'] = [];
        }

        $widgets[$widgetIndex]['pivot']['settings'] = $request->settings;

        // Save the updated widgets
        $preferences->update([
            'planning_widgets' => $widgets,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Widget settings updated successfully',
            'widget' => $widgets[$widgetIndex],
        ]);
    }

    /**
     * Toggle a widget's visibility.
     */
    public function toggleWidgetVisibility(Request $request, $widgetId)
    {
        $user = Auth::user();
        $preferences = $user->preference;

        if (! $preferences || ! $preferences->planning_widgets) {
            return response()->json([
                'success' => false,
                'message' => 'Widget not found',
            ], 404);
        }

        $widgets = $preferences->planning_widgets;
        $widgetIndex = -1;

        // Find the widget by ID
        foreach ($widgets as $index => $widget) {
            if ($widget['id'] === $widgetId) {
                $widgetIndex = $index;
                break;
            }
        }

        if ($widgetIndex === -1) {
            return response()->json([
                'success' => false,
                'message' => 'Widget not found',
            ], 404);
        }

        // Toggle the widget visibility
        if (! isset($widgets[$widgetIndex]['pivot'])) {
            $widgets[$widgetIndex]['pivot'] = [];
        }

        $isVisible = isset($widgets[$widgetIndex]['pivot']['is_visible']) ?
            $widgets[$widgetIndex]['pivot']['is_visible'] : true;

        $widgets[$widgetIndex]['pivot']['is_visible'] = ! $isVisible;

        // Save the updated widgets
        $preferences->update([
            'planning_widgets' => $widgets,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Widget visibility toggled successfully',
            'widget' => $widgets[$widgetIndex],
        ]);
    }

    /**
     * Remove a widget from the user's preferences.
     */
    public function removeWidget(Request $request, $widgetId)
    {
        $user = Auth::user();
        $preferences = $user->preference;

        if (! $preferences || ! $preferences->planning_widgets) {
            return response()->json([
                'success' => false,
                'message' => 'Widget not found',
            ], 404);
        }

        $widgets = $preferences->planning_widgets;
        $widgetIndex = -1;

        // Find the widget by ID
        foreach ($widgets as $index => $widget) {
            if ($widget['id'] === $widgetId) {
                $widgetIndex = $index;
                break;
            }
        }

        if ($widgetIndex === -1) {
            return response()->json([
                'success' => false,
                'message' => 'Widget not found',
            ], 404);
        }

        // Remove the widget
        array_splice($widgets, $widgetIndex, 1);

        // Save the updated widgets
        $preferences->update([
            'planning_widgets' => $widgets,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Widget removed successfully',
        ]);
    }
}
