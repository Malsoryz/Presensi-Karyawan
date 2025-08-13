<div class="flex flex-row gap-3">
    <div class="flex items-center gap-3">
        <x-heroicon-o-calendar class="text-primary-600 dark:text-primary-400 h-6 w-6 drop-shadow-sm"/>
        <span id="date" class="block">
            {{ $datetime->translatedFormat('l, d F Y') }}
        </span>
    </div>
    <div class="text-gray-950 dark:text-white">|</div>
    <div class="flex items-center gap-3">
        <x-heroicon-o-clock class="text-primary-600 dark:text-primary-400 h-6 w-6 drop-shadow-sm"/>
        <span id="time" class="block text-primary-600 dark:text-primary-400" 
            x-data="{
                time: null,
                updateTime() {
                    const now = new Date();
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    const seconds = String(now.getSeconds()).padStart(2, '0');
                    this.time = `${hours}:${minutes}:${seconds}`;
                },
                startTime() {
                    this.updateTime();
                    setInterval(() => this.updateTime(), 1000);
                }
            }" 
            x-init="startTime()"
            x-text="time"
        ></span>
    </div>
</div>
