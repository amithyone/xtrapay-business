<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * Display a listing of the tickets.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Ticket::query();

        // Optionally filter by status, priority, search
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        if ($request->has('priority') && $request->priority != '') {
            $query->where('priority', $request->priority);
        }
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('subject', 'like', "%$search%");
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'tickets' => $tickets->items(),
                'current_page' => $tickets->currentPage(),
                'last_page' => $tickets->lastPage(),
                'from' => $tickets->firstItem(),
                'to' => $tickets->lastItem(),
                'total' => $tickets->total(),
            ]);
        }

        return view('tickets.index', compact('tickets'));
    }

    /**
     * Store a newly created ticket.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->businessProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Please complete your business profile before creating a support ticket.'
            ], 422);
        }

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'priority' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $ticket = new Ticket();
        $ticket->user_id = $user->id;
        $ticket->business_profile_id = $user->businessProfile->id;
        $ticket->subject = $validated['subject'];
        $ticket->category = $validated['category'];
        $ticket->priority = $validated['priority'];
        $ticket->status = 'open';
        $ticket->message = $validated['message'];
        $ticket->save();

        // Save initial message
        $ticket->messages()->create([
            'user_id' => $user->id,
            'content' => $validated['message'],
            'is_support' => false,
        ]);

        return response()->json(['success' => true, 'message' => 'Ticket created successfully']);
    }

    /**
     * Display the specified ticket.
     */
    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $ticket->load('messages');

        return response()->json(['ticket' => $ticket]);
    }

    /**
     * Reply to a ticket.
     */
    public function reply(Request $request, Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $ticket->messages()->create([
            'user_id' => Auth::id(),
            'content' => $validated['message'],
            'is_support' => false,
        ]);

        return response()->json(['success' => true, 'message' => 'Reply added successfully']);
    }
}
