<?php

namespace App\Http\Controllers;

use App\Models\PredefinedMessage;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use App\Traits\LoadsCommonData;

class NotificationController extends Controller
{
    use LoadsCommonData;

    public function index()
    {
        session(['activeAdminContent' => 'notifications']);
        $data = $this->loadAllData();
        $data['news'] = $this->getEmptyNewsPaginator();
        $data['activeView'] = 'admin';
        $data['messages'] = PredefinedMessage::all();

        return view('index', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        PredefinedMessage::create($request->all());

        return redirect()->route('admin.notifications.index')->with('success', 'Mensaje creado correctamente.');
    }

    public function update(Request $request, PredefinedMessage $message)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $message->update($request->all());

        return redirect()->route('admin.notifications.index')->with('success', 'Mensaje actualizado correctamente.');
    }

    public function destroy(PredefinedMessage $message)
    {
        $message->delete();
        return redirect()->route('admin.notifications.index')->with('success', 'Mensaje eliminado correctamente.');
    }

    public function send(PredefinedMessage $message, WhatsAppService $whatsapp)
    {
        $success = $whatsapp->sendMessage($message->content);

        if ($success) {
            return redirect()->route('admin.notifications.index')->with('success', 'Notificación enviada correctamente.');
        } else {
            return redirect()->route('admin.notifications.index')->with('error', 'Error al enviar la notificación.');
        }
    }
}
