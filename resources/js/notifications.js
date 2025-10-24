class TransactionNotifications {
    constructor() {
        this.permission = Notification.permission;
        this.lastCount = 0;
        this.audio = null;
        this.enabled = localStorage.getItem('notifications_enabled') !== 'false';
        this.soundEnabled = localStorage.getItem('sound_enabled') !== 'false';
        
        this.init();
    }

    init() {
        // Precargar audio
        this.audio = new Audio('/sounds/notification.mp3');
        this.audio.volume = 0.5;

        // Escuchar eventos de Livewire
        document.addEventListener('livewire:init', () => {
            Livewire.on('monitorRefreshed', (event) => {
                this.handleUpdate(event.count);
            });
        });

        // Actualizar contador en título
        this.updateTitle(0);
    }

    async requestPermission() {
        if (this.permission === 'default') {
            this.permission = await Notification.requestPermission();
        }
        return this.permission === 'granted';
    }

    async handleUpdate(newCount) {
        // Si hay más transacciones que antes, notificar
        if (newCount > this.lastCount && this.lastCount > 0) {
            const diff = newCount - this.lastCount;
            await this.notify(diff);
        }

        this.lastCount = newCount;
        this.updateTitle(newCount);
    }

    async notify(newTransactions) {
        if (!this.enabled) return;

        // Verificar permisos
        if (this.permission !== 'granted') {
            const granted = await this.requestPermission();
            if (!granted) return;
        }

        // Crear notificación
        const notification = new Notification('Nueva transacción pendiente', {
            body: `${newTransactions} nueva${newTransactions > 1 ? 's' : ''} transacción${newTransactions > 1 ? 'es' : ''} esperando aprobación`,
            icon: '/favicon.ico',
            badge: '/favicon.ico',
            tag: 'transaction-pending',
            requireInteraction: false,
            silent: !this.soundEnabled
        });

        // Click en la notificación
        notification.onclick = () => {
            window.focus();
            notification.close();
        };

        // Reproducir sonido
        if (this.soundEnabled) {
            this.playSound();
        }

        // Auto-cerrar después de 5 segundos
        setTimeout(() => notification.close(), 5000);
    }

    playSound() {
        if (this.audio && this.soundEnabled) {
            this.audio.currentTime = 0;
            this.audio.play().catch(err => {
                console.log('No se pudo reproducir el sonido:', err);
            });
        }
    }

    updateTitle(count) {
        if (count > 0) {
            document.title = `(${count}) Transacciones Pendientes`;
        } else {
            document.title = 'Dashboard - Casino';
        }
    }

    enable() {
        this.enabled = true;
        localStorage.setItem('notifications_enabled', 'true');
    }

    disable() {
        this.enabled = false;
        localStorage.setItem('notifications_enabled', 'false');
    }

    enableSound() {
        this.soundEnabled = true;
        localStorage.setItem('sound_enabled', 'true');
    }

    disableSound() {
        this.soundEnabled = false;
        localStorage.setItem('sound_enabled', 'false');
    }

    isEnabled() {
        return this.enabled;
    }

    isSoundEnabled() {
        return this.soundEnabled;
    }
}

// Inicializar solo si estamos en la página del monitor
if (window.location.pathname.includes('/dashboard/transactions')) {
    window.transactionNotifications = new TransactionNotifications();
}