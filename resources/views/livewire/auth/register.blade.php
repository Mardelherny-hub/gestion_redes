@php
    $currentTenant = $this->tenant;
@endphp
<div>
    <!-- Título -->
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-white mb-2">Crear Cuenta</h2>
        <p class="text-gray-400 text-sm">🎁 Bono 30% en tu primera carga!</p>
    </div>

    <!-- Formulario -->
    <form wire:submit.prevent="register" class="space-y-4">
        
        <!-- Nombre completo -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">
                Nombre completo <span class="text-red-400">*</span>
            </label>
            <input 
                type="text" 
                wire:model.blur="name"
                placeholder="Juan Pérez"
                required
                class="w-full px-4 py-3 bg-white/5 border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:bg-white/10 transition
                    {{ $errors->has('name') ? 'border-red-500' : 'border-white/10 focus:border-white/30' }}"
            >
            @error('name') 
                <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p> 
            @enderror
        </div>

        <!-- Email -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">
                Email <span class="text-red-400">*</span>
            </label>
            <input 
                type="email" 
                wire:model.blur="email"
                placeholder="tu@email.com"
                required
                class="w-full px-4 py-3 bg-white/5 border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:bg-white/10 transition
                    {{ $errors->has('email') ? 'border-red-500' : 'border-white/10 focus:border-white/30' }}"
            >
            @error('email') 
                <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p> 
            @enderror
        </div>

        <!-- Teléfono -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">
                Teléfono Whatsapp <span class="text-red-400">*</span>
            </label>
            <input 
                type="text" 
                wire:model.blur="phone"
                placeholder="+54 9 11 1234-5678"
                required
                class="w-full px-4 py-3 bg-white/5 border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:bg-white/10 transition
                    {{ $errors->has('phone') ? 'border-red-500' : 'border-white/10 focus:border-white/30' }}"
            >
            @error('phone') 
                <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p> 
            @enderror
        </div>

        <!-- Contraseña -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">
                Contraseña <span class="text-red-400">*</span>
            </label>
            <div class="relative">
                <input 
                    type="{{ $showPassword ? 'text' : 'password' }}" 
                    wire:model.live="password"
                    placeholder="Mínimo 8 caracteres"
                    required
                    class="w-full px-4 py-3 pr-12 bg-white/5 border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:bg-white/10 transition
                        {{ $errors->has('password') ? 'border-red-500' : 'border-white/10 focus:border-white/30' }}"
                >
                <button 
                    type="button"
                    wire:click="$toggle('showPassword')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white transition"
                >
                    @if($showPassword)
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    @endif
                </button>
            </div>
            
            @if($password)
                @php
                    $bar1 = $passwordStrength !== '' ? 'bg-red-500' : 'bg-white/10';
                    $bar2 = $passwordStrength === 'medium' ? 'bg-yellow-500' : ($passwordStrength === 'strong' ? 'bg-green-500' : 'bg-white/10');
                    $bar3 = $passwordStrength === 'strong' ? 'bg-green-500' : 'bg-white/10';
                @endphp
                <div class="mt-2 flex gap-1">
                    <div class="h-1 flex-1 rounded-full {{ $bar1 }}"></div>
                    <div class="h-1 flex-1 rounded-full {{ $bar2 }}"></div>
                    <div class="h-1 flex-1 rounded-full {{ $bar3 }}"></div>
                </div>
                <p class="text-xs mt-1.5 {{ $passwordStrength === 'weak' ? 'text-red-400' : ($passwordStrength === 'medium' ? 'text-yellow-400' : 'text-green-400') }}">
                    Fortaleza: 
                    @if($passwordStrength === 'weak') Débil
                    @elseif($passwordStrength === 'medium') Media
                    @else Fuerte
                    @endif
                </p>
            @endif
            
            @error('password') 
                <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p> 
            @enderror
        </div>

        <!-- Confirmar Contraseña -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">
                Confirmar contraseña <span class="text-red-400">*</span>
            </label>
            <div class="relative">
                <input 
                    type="{{ $showPasswordConfirmation ? 'text' : 'password' }}" 
                    wire:model.blur="password_confirmation"
                    placeholder="Repite tu contraseña"
                    required
                    class="w-full px-4 py-3 pr-12 bg-white/5 border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:bg-white/10 transition
                        {{ $errors->has('password_confirmation') ? 'border-red-500' : 'border-white/10 focus:border-white/30' }}"
                >
                <button 
                    type="button"
                    wire:click="$toggle('showPasswordConfirmation')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white transition"
                >
                    @if($showPasswordConfirmation)
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    @endif
                </button>
            </div>
            @error('password_confirmation') 
                <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p> 
            @enderror
        </div>

        <!-- Código de Referido -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">
                Código de referido <span class="text-gray-500">(opcional)</span>
            </label>
            <div class="relative">
                <input 
                    type="text" 
                    wire:model.blur="referral_code"
                    placeholder="ABC12345"
                    maxlength="8"
                    class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-white/30 focus:bg-white/10 transition uppercase"
                >
                @if($referralCodeValid === true)
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-green-400 text-xl">✓</span>
                @elseif($referralCodeValid === false)
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-red-400 text-xl">✗</span>
                @endif
            </div>
            <p class="text-xs text-gray-400 mt-1.5">¿Te refirió alguien? Ingresa su código</p>
            @error('referral_code') 
                <p class="text-red-400 text-xs mt-1 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p> 
            @enderror
        </div>

        <!-- Términos y Condiciones -->
        <div class="pt-2">
            <div class="flex items-start gap-3">
                <input 
                    type="checkbox" 
                    wire:model="terms"
                    id="terms"
                    required
                    class="mt-0.5 w-4 h-4 rounded border-white/20 bg-white/5 focus:ring-2 focus:ring-offset-0"
                    style="color: {{ $currentTenant->primary_color }}"
                >
                <label for="terms" class="text-sm text-gray-300 leading-tight">
                    Acepto los <a href="#" class="text-purple-400 hover:text-purple-300 underline">términos y condiciones</a> <span class="text-red-400">*</span>
                </label>
            </div>
            @error('terms') 
                <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1 ml-7">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p> 
            @enderror
        </div>

        <!-- Botón de Registro -->
        <button 
            type="submit"
            wire:loading.attr="disabled"
            class="w-full py-3.5 rounded-lg text-white font-semibold text-base transition hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed mt-6"
            style="background: linear-gradient(135deg, {{ $currentTenant->primary_color }} 0%, {{ $currentTenant->secondary_color }} 100%);"
        >
            <span wire:loading.remove wire:target="register">Crear Cuenta</span>
            <span wire:loading wire:target="register" class="flex items-center justify-center gap-2">
                <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Procesando...
            </span>
        </button>

        <!-- Link a Login -->
        <p class="text-center text-sm text-gray-400 mt-6">
            ¿Ya tienes una cuenta? 
            <a href="{{ route('player.login') }}" class="text-purple-400 hover:text-purple-300 font-semibold underline">
                Inicia sesión
            </a>
        </p>
    </form>
</div>