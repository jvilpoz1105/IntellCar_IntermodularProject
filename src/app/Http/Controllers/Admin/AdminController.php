<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppUser;
use App\Models\CarAdvert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // ─── Autenticación ────────────────────────────────────────────────────────

    public function showLogin()
    {
        if (Auth::check() && Auth::user()->user_tag === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email_address' => ['required', 'email'],
            'user_password' => ['required'],
        ]);

        $attempt = Auth::attempt([
            'email_address' => $credentials['email_address'],
            'password'      => $credentials['user_password'],
        ], $request->boolean('remember'));

        if (!$attempt) {
            return back()->withErrors([
                'email_address' => 'Credenciales incorrectas.',
            ])->onlyInput('email_address');
        }

        if (Auth::user()->user_tag !== 'admin') {
            Auth::logout();
            return back()->withErrors([
                'email_address' => 'No tienes permisos de administrador.',
            ])->onlyInput('email_address');
        }

        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    // ─── Dashboard ────────────────────────────────────────────────────────────

    public function dashboard()
    {
        $stats = [
            'total_users'         => AppUser::count(),
            'active_users'        => AppUser::where('is_active', true)->count(),
            'pending_delete_users'=> AppUser::whereNotNull('onDeleteRequest')->count(),
            'total_adverts'       => CarAdvert::count(),
            'visible_adverts'     => CarAdvert::where('visible', true)->count(),
            'pending_delete_ads'  => CarAdvert::whereNotNull('onDeleteRequest')->count(),
        ];

        $latest_users   = AppUser::latest('created_at')->take(5)->get();
        $latest_adverts = CarAdvert::with('seller')->latest('publish_date')->take(5)->get();

        return view('admin.dashboard', compact('stats', 'latest_users', 'latest_adverts'));
    }

    // ─── Gestión de Usuarios ──────────────────────────────────────────────────

    public function usersIndex(Request $request)
    {
        $query = AppUser::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                  ->orWhere('email_address', 'like', "%{$search}%");
            });
        }

        if ($tag = $request->input('tag')) {
            $query->where('user_tag', $tag);
        }

        if ($request->input('pending_delete') === '1') {
            $query->whereNotNull('onDeleteRequest');
        }

        $users = $query->latest('created_at')->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function usersShow(AppUser $user)
    {
        $user->load('adverts', 'garage', 'paddock');

        return view('admin.users.show', compact('user'));
    }

    public function usersToggleActive(AppUser $user)
    {
        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activado' : 'desactivado';

        return back()->with('success', "Usuario {$user->user_name} {$status} correctamente.");
    }

    public function usersDestroy(AppUser $user)
    {
        // No permitir que el admin se borre a sí mismo
        if ($user->user_id === Auth::id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $user->adverts()->delete();
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Usuario {$user->user_name} eliminado definitivamente.");
    }

    // ─── Gestión de Anuncios ──────────────────────────────────────────────────

    public function advertsIndex(Request $request)
    {
        $query = CarAdvert::with('seller');

        if ($search = $request->input('search')) {
            $query->where('ad_title', 'like', "%{$search}%");
        }

        if ($type = $request->input('ad_type')) {
            $query->where('ad_type', $type);
        }

        if ($request->input('pending_delete') === '1') {
            $query->whereNotNull('onDeleteRequest');
        }

        if ($request->input('hidden') === '1') {
            $query->where('visible', false);
        }

        $adverts = $query->latest('publish_date')->paginate(20)->withQueryString();

        return view('admin.adverts.index', compact('adverts'));
    }

    public function advertsShow(CarAdvert $advert)
    {
        $advert->load('seller', 'model', 'engine', 'media');

        return view('admin.adverts.show', compact('advert'));
    }

    public function advertsToggleVisible(CarAdvert $advert)
    {
        $advert->update(['visible' => !$advert->visible]);

        $status = $advert->visible ? 'visible' : 'oculto';

        return back()->with('success', "Anuncio marcado como {$status}.");
    }

    public function advertsDestroy(CarAdvert $advert)
    {
        $advert->media()->delete();
        $advert->delete();

        return redirect()->route('admin.adverts.index')
            ->with('success', 'Anuncio eliminado definitivamente.');
    }
}
