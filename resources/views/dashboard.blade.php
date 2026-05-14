<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 lg:gap-8">
            <!-- Left: Logo & Title -->
            <div class="flex items-center gap-3 w-48 lg:w-64 flex-shrink-0">
                <h2 class="font-heading font-bold text-xl lg:text-2xl text-white tracking-tight whitespace-nowrap">
                    {{ __('Trickle Up Drive') }}
                </h2>
            </div>

            <!-- Center: Search Bar -->
            <div class="flex-1 max-w-3xl hidden md:block" x-data="searchBar()">
                <div class="relative group">
                    <form action="{{ route('dashboard') }}" method="GET" class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-primary-500 transition-colors"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" name="search" x-model="query" @input.debounce.300ms="search()"
                            @focus="if(query.length >= 2) showResults = true"
                            placeholder="Search in Trickle Up..."
                            autocomplete="off"
                            class="block w-full pl-11 pr-12 py-2.5 bg-white/10 border-transparent rounded-full text-white placeholder-gray-500 focus:bg-dark-card focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all duration-300 sm:text-base shadow-inner">
                        
                        <!-- Right Icons: Clear and Filter -->
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center gap-2">
                            <button type="button" x-show="query" @click="clear()" class="text-gray-400 hover:text-white transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                            <button type="button" class="text-gray-400 hover:text-primary-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                            </button>
                        </div>
                    </form>

                    <!-- Search Results Dropdown -->
                    <div x-show="showResults" @click.away="showResults = false" x-transition
                        class="absolute left-0 right-0 mt-2 bg-dark-card border border-white/10 rounded-2xl shadow-2xl z-[100] overflow-hidden max-h-[80vh] flex flex-col"
                        style="display: none;">
                        
                        <!-- Loading Indicator -->
                        <div x-show="loading" class="p-4 flex justify-center">
                            <svg class="animate-spin h-6 w-6 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>

                        <div class="overflow-y-auto" x-show="!loading">
                            <!-- No Results -->
                            <div x-show="results.folders.length === 0 && results.files.length === 0" class="p-8 text-center text-gray-500">
                                No results found for "<span x-text="query"></span>"
                            </div>

                            <!-- Results List -->
                            <div class="py-2">
                                <!-- Folders -->
                                <template x-for="folder in results.folders" :key="'folder-'+folder.id">
                                    <a :href="folder.url" class="flex items-center gap-4 px-5 py-3 hover:bg-white/5 transition-colors group">
                                        <div class="p-2 rounded-lg bg-secondary-500/10 text-secondary-500 group-hover:bg-primary-500/20 group-hover:text-primary-500 transition-colors">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path></svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold text-white truncate" x-html="highlight(folder.name)"></p>
                                            <p class="text-xs text-gray-500 mt-0.5" x-text="folder.owner"></p>
                                        </div>
                                        <div class="text-xs text-gray-500" x-text="folder.updated_at"></div>
                                    </a>
                                </template>

                                <!-- Files -->
                                <template x-for="file in results.files" :key="'file-'+file.id">
                                    <a :href="file.url" target="_blank" class="flex items-center gap-4 px-5 py-3 hover:bg-white/5 transition-colors group">
                                        <div class="p-2 rounded-lg bg-white/5 text-gray-400 group-hover:bg-primary-500/20 group-hover:text-primary-500 transition-colors">
                                            <template x-if="file.mime_type.startsWith('image/')">
                                                <svg class="w-5 h-5 text-secondary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </template>
                                            <template x-if="file.mime_type.startsWith('video/')">
                                                <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                            </template>
                                            <template x-if="file.mime_type === 'application/pdf'">
                                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </template>
                                            <template x-if="!file.mime_type.startsWith('image/') && !file.mime_type.startsWith('video/') && file.mime_type !== 'application/pdf'">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                            </template>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold text-white truncate" x-html="highlight(file.name)"></p>
                                            <p class="text-xs text-gray-500 mt-0.5" x-text="file.owner"></p>
                                        </div>
                                        <div class="text-xs text-gray-500" x-text="file.updated_at"></div>
                                    </a>
                                </template>
                            </div>
                        </div>

                        <!-- Bottom Links -->
                        <div class="border-t border-white/10 p-3 flex items-center justify-between bg-white/5">
                            <a :href="'{{ route('dashboard') }}?search=' + encodeURIComponent(query)" class="text-xs font-bold text-primary-500 hover:underline">Advanced search</a>
                            <a :href="'{{ route('dashboard') }}?search=' + encodeURIComponent(query)" class="flex items-center gap-2 text-xs font-bold text-primary-500 hover:underline">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7l4 4m0 0l4-4m-4 4V3"></path></svg>
                                All results
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Actions -->
            <div class="flex items-center justify-end gap-2 w-48 lg:w-64 flex-shrink-0">
                <button
                    class="p-2 text-gray-400 hover:text-gray-400 hover:bg-white/10 rounded-full transition-colors hidden sm:block">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </button>
                <button
                    class="p-2 text-gray-400 hover:text-gray-400 hover:bg-white/10 rounded-full transition-colors hidden sm:block mr-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </button>

                <!-- User Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.away="open = false"
                        class="flex items-center gap-2 focus:outline-none p-0.5 rounded-full hover:ring-4 hover:ring-primary-500/20 transition-all duration-300">
                        <div
                            class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 text-dark-bg flex items-center justify-center font-bold text-lg shadow-lg border border-white/20">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    </button>
 
                    <div x-show="open" x-transition
                        class="absolute right-0 z-50 mt-3 w-64 bg-dark-card rounded-2xl shadow-2xl border border-white/10 py-1 hidden"
                        :class="{'hidden': !open}">
                        <div class="px-5 py-4 border-b border-white/10 bg-white/5 rounded-t-2xl">
                            <p class="text-sm font-bold text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500 truncate mt-0.5">{{ Auth::user()->email }}</p>
                        </div>
                        <div class="p-2">
                            <a href="{{ route('profile.edit') }}"
                                class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-300 rounded-xl hover:bg-primary-500/10 hover:text-primary-500 transition-colors group">
                                <div class="p-2 rounded-lg bg-white/5 group-hover:bg-primary-500/20 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                Profile Settings
                            </a>
                        </div>
                        <div class="p-2 border-t border-white/10">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="flex w-full items-center gap-3 px-3 py-2.5 text-sm font-medium text-red-400 rounded-xl hover:bg-red-500/10 hover:text-red-500 transition-colors group">
                                    <div class="p-2 rounded-lg bg-white/5 group-hover:bg-red-500/20 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                            </path>
                                        </svg>
                                    </div>
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div x-data="{ uploading: false, uploadMinimized: false, uploadFiles: [], viewMode: localStorage.getItem('viewMode') || 'list',
        async toggleStar(url, callback) {
            try {
                let response = await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' } });
                let data = await response.json();
                if (data.success) { callback(data.is_starred); window.dispatchEvent(new CustomEvent('show-toast', { detail: { type: 'success', message: data.message } })); }
                else { window.dispatchEvent(new CustomEvent('show-toast', { detail: { type: 'warning', message: data.message || 'Error occurred' } })); }
            } catch (error) { window.dispatchEvent(new CustomEvent('show-toast', { detail: { type: 'warning', message: 'Network error occurred' } })); }
        },
        async submitAction(event, hideModalCallback = null) {
            const form = event.target;
            if (form.hasAttribute('data-confirm')) {
                if (!confirm(form.getAttribute('data-confirm'))) return;
            }
            let isUpload = form.enctype === 'multipart/form-data';
            if (isUpload) { this.uploading = true; this.uploadMinimized = false; }
            try {
                let response = await fetch(form.action || form.getAttribute('action'), {
                    method: form.method ? (form.method.toUpperCase() === 'GET' ? 'GET' : 'POST') : 'POST',
                    headers: { 'Accept': 'application/json' },
                    body: new FormData(form)
                });
                let data = await response.json();
                if (data.success) {
                    if (isUpload) {
                        this.uploadFiles = this.uploadFiles.map(f => ({...f, status: 'done'}));
                    }
                    window.dispatchEvent(new CustomEvent('show-toast', { detail: { type: 'success', message: data.message } }));
                    if (hideModalCallback) hideModalCallback();
                    this.refreshDashboard();
                } else {
                    if (isUpload) this.uploadFiles = this.uploadFiles.map(f => ({...f, status: 'error'}));
                    window.dispatchEvent(new CustomEvent('show-toast', { detail: { type: 'warning', message: data.message || 'Error' } }));
                }
            } catch (error) {
                if (isUpload) this.uploadFiles = this.uploadFiles.map(f => ({...f, status: 'error'}));
                window.dispatchEvent(new CustomEvent('show-toast', { detail: { type: 'warning', message: 'Network error' } }));
            }
        },
        async refreshDashboard() {
            try {
                let response = await fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                let html = await response.text();
                let doc = new DOMParser().parseFromString(html, 'text/html');
                if (doc.getElementById('dashboard-main')) document.getElementById('dashboard-main').innerHTML = doc.getElementById('dashboard-main').innerHTML;
                if (doc.getElementById('dashboard-sidebar')) document.getElementById('dashboard-sidebar').innerHTML = doc.getElementById('dashboard-sidebar').innerHTML;
            } catch (e) {
                window.location.reload();
            }
        }
    }">
    <div x-init="$watch('viewMode', val => localStorage.setItem('viewMode', val))"
        @start-upload.window="uploading = true; uploadFiles = $event.detail.files.map(f => ({ name: f.name, status: 'uploading' }))"
        class="w-full px-6 sm:px-8 lg:px-10 py-8 flex flex-col md:flex-row gap-8 relative">

        <!-- Google Drive Style Upload Panel -->
        <div x-show="uploading" style="display: none;"
             class="fixed bottom-0 right-10 w-80 bg-dark-card rounded-t-lg shadow-[0_-2px_15px_rgba(0,0,0,0.15)] z-[9900] overflow-hidden flex flex-col font-sans transition-all duration-300"
             :class="uploadMinimized ? 'h-[48px]' : 'max-h-96'"
             x-transition:enter="transition transform ease-out duration-300"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition transform ease-in duration-200"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full">
             
            <!-- Header -->
            <div @click="uploadMinimized = !uploadMinimized" class="bg-[#323232] text-white px-4 py-3 flex items-center justify-between cursor-pointer rounded-t-lg h-[48px] flex-shrink-0">
                <h3 class="text-[15px] font-medium" x-text="uploadFiles.some(f => f.status === 'uploading') ? ('Uploading ' + uploadFiles.length + (uploadFiles.length === 1 ? ' item' : ' items')) : (uploadFiles.length + (uploadFiles.length === 1 ? ' upload complete' : ' uploads complete'))"></h3>
                <div class="flex items-center gap-2">
                    <button class="hover:bg-dark-card/10 p-1 rounded transition-colors" @click.stop="uploadMinimized = !uploadMinimized">
                        <svg class="w-5 h-5 transition-transform duration-200" :class="uploadMinimized ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <button @click.stop="uploading = false; uploadFiles = []" class="hover:bg-dark-card/10 p-1 rounded transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>
            
            <div x-show="!uploadMinimized" class="flex flex-col flex-1 overflow-hidden" x-transition.opacity>
                <!-- Sub-header -->
                <div class="bg-[#f8f9fa] border-b border-white/10 px-4 py-2.5 flex items-center justify-between flex-shrink-0">
                    <span class="text-sm text-gray-400" x-text="uploadFiles.some(f => f.status === 'uploading') ? 'Starting uploads...' : 'Uploads complete'"></span>
                    <button x-show="uploadFiles.some(f => f.status === 'uploading')" @click="uploading = false; uploadFiles = []" class="text-primary-500 text-[13px] font-semibold hover:text-primary-700 uppercase tracking-wider">Cancel</button>
                </div>
                
                <!-- File List -->
                <div class="overflow-y-auto bg-dark-card flex-1 min-h-[60px]">
                    <template x-for="file in uploadFiles" :key="file.name">
                        <div class="px-4 py-3 flex items-center justify-between border-b border-white/10 last:border-0 hover:bg-white/5">
                            <div class="flex items-center gap-3 overflow-hidden">
                                <!-- Dummy image icon -->
                                <div class="w-6 h-6 bg-red-50 rounded flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3.5 h-3.5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path></svg>
                                </div>
                                <span class="text-sm text-gray-200 truncate font-medium" x-text="file.name"></span>
                            </div>
                            
                            <div class="flex-shrink-0 ml-3">
                                <!-- Spinning Circle -->
                                <svg x-show="file.status === 'uploading'" class="w-5 h-5 text-gray-300 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="#1a73e8" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <!-- Checkmark -->
                                <svg x-show="file.status === 'done'" class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <!-- Error -->
                                <svg x-show="file.status === 'error'" class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div id="dashboard-sidebar" class="w-full md:w-64 flex-shrink-0">
            <!-- New Button Dropdown -->
            <div class="relative mb-6" x-data="{ open: false }">
                <button @click="open = !open" @click.away="open = false"
                    class="w-full flex items-center justify-center gap-2 py-3.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-dark-bg bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-300 transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    New
                </button>

                <div x-show="open" x-transition style="display: none;"
                    class="absolute z-50 mt-2 w-full bg-dark-card rounded-xl shadow-lg border border-white/10 py-2">
                    <button type="button" @click="$dispatch('open-folder-modal'); open = false"
                        class="w-full text-left flex items-center gap-3 px-4 py-2 text-sm text-gray-200 hover:bg-white/5 hover:text-primary-500 transition-colors">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z">
                            </path>
                        </svg>
                        New Folder
                    </button>
                    <div class="border-t border-white/10 my-1"></div>
                    <form action="{{ route('files.upload') }}" method="POST" enctype="multipart/form-data" class="m-0" @submit.prevent="submitAction($event)">
                        @csrf
                        <input type="hidden" name="parent_id" value="{{ $parentId }}">
                        <label
                            class="w-full flex items-center gap-3 px-4 py-2 text-sm text-gray-200 hover:bg-white/5 hover:text-primary-500 cursor-pointer transition-colors">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                </path>
                            </svg>
                            File Upload
                            <input type="file" name="files[]" multiple class="hidden"
                                @change="$dispatch('start-upload', { files: Array.from($event.target.files) }); submitAction({ target: $event.target.form, preventDefault: () => {} })">
                        </label>
                    </form>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="space-y-1">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 {{ (isset($currentTab) && $currentTab === 'my_drive') ? 'bg-primary-500/10 text-primary-500' : 'text-gray-400 hover:bg-white/10 hover:text-white' }} rounded-xl font-medium transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path></svg>
                    My Drive
                </a>
                <a href="{{ route('recent') }}" class="flex items-center gap-3 px-4 py-2.5 {{ (isset($currentTab) && $currentTab === 'recent') ? 'bg-primary-500/10 text-primary-500' : 'text-gray-400 hover:bg-white/10 hover:text-white' }} rounded-xl font-medium transition-colors">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Recent
                </a>
                <a href="{{ route('starred') }}" class="flex items-center gap-3 px-4 py-2.5 {{ (isset($currentTab) && $currentTab === 'starred') ? 'bg-primary-500/10 text-primary-500' : 'text-gray-400 hover:bg-white/10 hover:text-white' }} rounded-xl font-medium transition-colors">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                    Starred
                </a>
                <a href="{{ route('trash') }}" class="flex items-center gap-3 px-4 py-2.5 {{ (isset($currentTab) && $currentTab === 'trash') ? 'bg-primary-500/10 text-primary-500' : 'text-gray-400 hover:bg-white/10 hover:text-white' }} rounded-xl font-medium transition-colors">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Trash
                </a>
            </nav>

            <!-- Storage Status -->
            @php
                $totalBytes = \App\Models\FileEntry::where('user_id', Auth::id())->sum('size');
                $maxBytes = 15 * 1024 * 1024 * 1024; // 15 GB
                $percentage = min(100, ($totalBytes / $maxBytes) * 100);
                
                $formatBytes = function($bytes) {
                    if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
                    if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
                    if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
                    return $bytes . ' B';
                };
            @endphp
            <div class="mt-8 px-4">
                <div class="flex justify-between items-center text-sm mb-2">
                    <span class="text-gray-400 font-medium">Storage</span>
                    <span class="text-white font-bold">{{ $formatBytes($totalBytes) }} / 15 GB</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-primary-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div id="dashboard-main"
            class="flex-1 bg-dark-card rounded-2xl shadow-xl border border-white/10 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <!-- Breadcrumb -->
                    <div class="flex items-center gap-2 text-sm font-medium text-gray-500">
                        @if(request('search'))
                            <span class="text-white font-bold">Search results for "{{ request('search') }}"</span>
                            <a href="{{ route('dashboard') }}" class="ml-2 text-primary-500 hover:underline">Clear</a>
                        @else
                            <a href="{{ route('dashboard') }}" class="hover:text-primary-500 transition-colors">My Drive</a>
                            @foreach($breadcrumbs as $breadcrumb)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                    </path>
                                </svg>
                                <a href="{{ route('dashboard', ['folder' => $breadcrumb->id]) }}"
                                    class="hover:text-primary-500 transition-colors {{ $loop->last ? 'text-white font-bold' : '' }}">{{ $breadcrumb->name }}</a>
                            @endforeach
                        @endif
                    </div>
                    <!-- View Toggle -->
                    <div class="flex items-center bg-white/10 rounded-lg p-1">
                        <button @click="viewMode = 'grid'"
                            :class="{'bg-dark-card shadow-sm text-white': viewMode === 'grid', 'text-gray-500 hover:text-gray-200': viewMode !== 'grid'}"
                            class="p-1.5 rounded-md transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                                </path>
                            </svg>
                        </button>
                        <button @click="viewMode = 'list'"
                            :class="{'bg-dark-card shadow-sm text-white': viewMode === 'list', 'text-gray-500 hover:text-gray-200': viewMode !== 'list'}"
                            class="p-1.5 rounded-md transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Folders Section -->
                @if(!isset($currentTab) || $currentTab !== 'recent')
                <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Folders</h3>

                <!-- Folders Grid View -->
                <div x-show="viewMode === 'grid'"
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-8">
                    @forelse($folders as $folder)
                        <div
                            class="relative group bg-dark-card border border-white/10 rounded-xl hover:border-primary-400 hover:shadow-md transition-all duration-300">
                            <a href="{{ route('dashboard', ['folder' => $folder->id]) }}"
                                class="flex items-center gap-4 p-4 cursor-pointer">
                                <div class="p-2 rounded-lg bg-secondary-500/10 group-hover:bg-primary-100 transition-colors">
                                    <svg class="w-8 h-8 text-secondary-500 group-hover:text-primary-500 transition-colors"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z">
                                        </path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0 pr-8">
                                    <h4 class="text-sm font-bold text-white truncate">{{ $folder->name }}</h4>
                                    <p class="text-xs text-gray-500 truncate mt-0.5">{{ $folder->files_count ?? 0 }} items
                                    </p>
                                </div>
                            </a>
                            <!-- Folder Actions Dropdown -->
                            <div class="absolute top-4 right-2" x-data="{ actionOpen: false, isStarred: {{ $folder->is_starred ? 'true' : 'false' }} }">
                                <button @click="actionOpen = !actionOpen" @click.away="actionOpen = false"
                                    class="p-1.5 text-gray-400 hover:text-gray-400 rounded-md hover:bg-white/10 transition-colors opacity-0 group-hover:opacity-100 focus:opacity-100">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z">
                                        </path>
                                    </svg>
                                </button>
                                <div x-show="actionOpen" x-transition style="display: none;" class="absolute right-0 z-10 mt-1 w-40 bg-dark-card rounded-xl shadow-lg border border-white/10 py-1">
                                    @if(isset($currentTab) && $currentTab === 'trash')
                                        <form action="{{ route('folders.restore', $folder->id) }}" method="POST" @submit.prevent="submitAction($event)">
                                            @csrf
                                            <button type="submit" class="w-full text-left flex items-center gap-3 px-4 py-2 text-sm text-gray-200 hover:bg-white/5">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                                Restore
                                            </button>
                                        </form>
                                        <form action="{{ route('folders.force_delete', $folder->id) }}" method="POST" data-confirm="Permanently delete this folder?" @submit.prevent="submitAction($event)">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="w-full text-left flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                Delete Forever
                                            </button>
                                        </form>
                                    @else
                                        <button @click="$dispatch('open-rename-modal', { id: {{ $folder->id }}, name: '{{ addslashes($folder->name) }}', type: 'folder' }); actionOpen = false" class="w-full text-left flex items-center gap-3 px-4 py-2 text-sm text-gray-200 hover:bg-white/5 hover:text-primary-500">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            Rename
                                        </button>
                                        <button type="button" @click="toggleStar('{{ route('folders.star', $folder->id) }}', (val) => isStarred = val)" class="w-full text-left flex items-center gap-3 px-4 py-2 text-sm text-gray-200 hover:bg-white/5">
                                            <svg class="w-4 h-4" :class="isStarred ? 'text-yellow-400' : 'text-gray-400'" :fill="isStarred ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                                            <span x-text="isStarred ? 'Unstar' : 'Star'"></span>
                                        </button>
                                        <form action="{{ route('folders.delete', $folder->id) }}" method="POST" data-confirm="Move this folder to trash?" @submit.prevent="submitAction($event)">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="w-full text-left flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                Trash
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div
                            class="col-span-full py-8 text-center text-gray-500 border-2 border-dashed border-white/10 rounded-xl">
                            No folders found.
                        </div>
                    @endforelse
                </div>

                <!-- Folders List View -->
                <div x-show="viewMode === 'list'" style="display: none;"
                    class="overflow-x-auto mb-8 border border-white/10 rounded-xl">
                    <table class="min-w-full divide-y divide-white/5">
                        <tbody class="bg-dark-card divide-y divide-white/5">
                            @foreach($folders as $folder)
                                <tr x-data="{ isStarred: {{ $folder->is_starred ? 'true' : 'false' }} }" class="hover:bg-white/5 transition-colors group">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <a href="{{ route('dashboard', ['folder' => $folder->id]) }}"
                                            class="flex items-center gap-3 cursor-pointer">
                                            <div
                                                class="p-1.5 rounded-lg bg-secondary-500/10 group-hover:bg-primary-100 transition-colors">
                                                <svg class="w-6 h-6 text-secondary-500 group-hover:text-primary-500 transition-colors"
                                                    fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <span
                                                class="text-sm font-bold text-white truncate max-w-[200px]">{{ $folder->name }}</span>
                                        </a>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $folder->files_count ?? 0 }} items</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            @if(isset($currentTab) && $currentTab === 'trash')
                                                <form action="{{ route('folders.restore', $folder->id) }}" method="POST" class="inline-block m-0" @submit.prevent="submitAction($event)">
                                                    @csrf
                                                    <button type="submit" class="text-gray-400 hover:text-primary-500 p-2 rounded-lg hover:bg-primary-50 transition-colors" title="Restore">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                                    </button>
                                                </form>
                                                <form action="{{ route('folders.force_delete', $folder->id) }}" method="POST" data-confirm="Permanently delete this folder?" class="inline-block m-0" @submit.prevent="submitAction($event)">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-gray-400 hover:text-red-600 p-2 rounded-lg hover:bg-red-50 transition-colors" title="Delete Forever">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button" @click="toggleStar('{{ route('folders.star', $folder->id) }}', (val) => isStarred = val)" :class="isStarred ? 'text-yellow-400' : 'text-gray-400'" class="hover:text-yellow-500 p-2 rounded-lg hover:bg-yellow-50 transition-colors inline-block m-0" title="Toggle Star">
                                                    <svg class="w-5 h-5" :fill="isStarred ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                                                </button>
                                                <button @click="$dispatch('open-rename-modal', { id: {{ $folder->id }}, name: '{{ addslashes($folder->name) }}', type: 'folder' })" class="text-gray-400 hover:text-primary-500 p-2 rounded-lg hover:bg-primary-50 transition-colors" title="Rename">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                </button>
                                                <form action="{{ route('folders.delete', $folder->id) }}" method="POST" data-confirm="Move this folder to trash?" class="inline-block m-0" @submit.prevent="submitAction($event)">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-gray-400 hover:text-red-600 p-2 rounded-lg hover:bg-red-50 transition-colors" title="Trash">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Files Section -->
                @endif
                <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Files</h3>

                <!-- Files List View -->
                <div x-show="viewMode === 'list'" class="overflow-x-auto border border-white/10 rounded-xl">
                    <table class="min-w-full divide-y divide-white/5">
                        <thead class="bg-white/5/50">
                            <tr>
                                <th scope="col"
                                    class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Name</th>
                                <th scope="col"
                                    class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Owner</th>
                                <th scope="col"
                                    class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Last Modified</th>
                                <th scope="col"
                                    class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Size</th>
                                <th scope="col"
                                    class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-dark-card divide-y divide-white/5">
                            @forelse($files as $file)
                                <tr x-data="{ isStarred: {{ $file->is_starred ? 'true' : 'false' }} }" class="hover:bg-white/5 transition-colors group">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <a href="{{ asset('storage/' . $file->path) }}" target="_blank"
                                            class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 rounded-lg bg-white/10 group-hover:bg-dark-card flex items-center justify-center text-gray-400 shadow-sm transition-colors">
                                                @if(Str::startsWith($file->mime_type, 'image/'))
                                                    <svg class="w-5 h-5 text-secondary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                @elseif(Str::startsWith($file->mime_type, 'video/'))
                                                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                @elseif($file->mime_type === 'application/pdf')
                                                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                @else
                                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                @endif
                                            </div>
                                            <span
                                                class="text-sm font-bold text-white truncate max-w-[200px] hover:text-primary-500 hover:underline transition-colors">{{ $file->name }}</span>
                                        </a>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">Me</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $file->updated_at->format('M d, Y') }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($file->size / 1024, 2) }} KB</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            @if(isset($currentTab) && $currentTab === 'trash')
                                                <form action="{{ route('files.restore', $file->id) }}" method="POST" class="inline-block m-0" @submit.prevent="submitAction($event)">
                                                    @csrf
                                                    <button type="submit" class="text-gray-400 hover:text-primary-500 p-2 rounded-lg hover:bg-primary-50 transition-colors" title="Restore">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                                    </button>
                                                </form>
                                                <form action="{{ route('files.force_delete', $file->id) }}" method="POST" data-confirm="Permanently delete this file?" class="inline-block m-0" @submit.prevent="submitAction($event)">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-gray-400 hover:text-red-600 p-2 rounded-lg hover:bg-red-50 transition-colors" title="Delete Forever">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('files.download', $file->id) }}" class="text-gray-400 hover:text-primary-500 p-2 rounded-lg hover:bg-primary-50 transition-colors" title="Download">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                </a>
                                                <button type="button" @click="toggleStar('{{ route('files.star', $file->id) }}', (val) => isStarred = val)" :class="isStarred ? 'text-yellow-400' : 'text-gray-400'" class="hover:text-yellow-500 p-2 rounded-lg hover:bg-yellow-50 transition-colors inline-block m-0" title="Toggle Star">
                                                    <svg class="w-5 h-5" :fill="isStarred ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                                                </button>
                                                <button @click="$dispatch('open-rename-modal', { id: {{ $file->id }}, name: '{{ addslashes($file->name) }}', type: 'file' })" class="text-gray-400 hover:text-primary-500 p-2 rounded-lg hover:bg-primary-50 transition-colors" title="Rename">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                </button>
                                                <form action="{{ route('files.delete', $file->id) }}" method="POST" data-confirm="Move this file to trash?" class="inline-block m-0" @submit.prevent="submitAction($event)">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-gray-400 hover:text-red-600 p-2 rounded-lg hover:bg-red-50 transition-colors" title="Trash">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5"
                                        class="px-4 py-8 text-center text-gray-500 border-2 border-dashed border-white/10 rounded-xl">
                                        No files found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Files Grid View -->
                <div x-show="viewMode === 'grid'" style="display: none;"
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @forelse($files as $file)
                        <div x-data="{ isStarred: {{ $file->is_starred ? 'true' : 'false' }} }"
                            class="relative group bg-dark-card border border-white/10 rounded-xl hover:border-primary-400 hover:shadow-md transition-all duration-300 flex flex-col">
                            <a href="{{ asset('storage/' . $file->path) }}" target="_blank"
                                class="flex-1 p-4 flex flex-col items-center justify-center gap-3 cursor-pointer">
                                <div
                                    class="w-16 h-16 rounded-2xl bg-white/5 flex items-center justify-center text-gray-400 group-hover:text-primary-500 group-hover:bg-primary-50 transition-colors">
                                    @if(Str::startsWith($file->mime_type, 'image/'))
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    @elseif(Str::startsWith($file->mime_type, 'video/'))
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    @elseif($file->mime_type === 'application/pdf')
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    @else
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    @endif
                                </div>
                                <div class="text-center w-full px-2">
                                    <h4 class="text-sm font-bold text-white truncate">{{ $file->name }}</h4>
                                    <p class="text-xs text-gray-500 mt-1">{{ number_format($file->size / 1024, 2) }} KB</p>
                                </div>
                            </a>
                            <div class="border-t border-white/10 p-2 flex items-center justify-around opacity-0 group-hover:opacity-100 transition-opacity bg-white/5/50 rounded-b-xl">
                                @if(isset($currentTab) && $currentTab === 'trash')
                                    <form action="{{ route('files.restore', $file->id) }}" method="POST" class="m-0" @submit.prevent="submitAction($event)">
                                        @csrf
                                        <button type="submit" class="p-1.5 text-gray-500 hover:text-primary-500 rounded hover:bg-dark-card transition-colors" title="Restore">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                        </button>
                                    </form>
                                    <form action="{{ route('files.force_delete', $file->id) }}" method="POST" data-confirm="Permanently delete this file?" class="m-0" @submit.prevent="submitAction($event)">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-gray-500 hover:text-red-600 rounded hover:bg-dark-card transition-colors" title="Delete Forever">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('files.download', $file->id) }}" class="p-1.5 text-gray-500 hover:text-primary-500 rounded hover:bg-dark-card transition-colors" title="Download">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    </a>
                                    <button type="button" @click="toggleStar('{{ route('files.star', $file->id) }}', (val) => isStarred = val)" :class="isStarred ? 'text-yellow-400' : 'text-gray-500'" class="p-1.5 hover:text-yellow-500 rounded hover:bg-dark-card transition-colors m-0" title="Toggle Star">
                                        <svg class="w-4 h-4" :fill="isStarred ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                                    </button>
                                    <button @click="$dispatch('open-rename-modal', { id: {{ $file->id }}, name: '{{ addslashes($file->name) }}', type: 'file' })" class="p-1.5 text-gray-500 hover:text-primary-500 rounded hover:bg-dark-card transition-colors" title="Rename">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                    <form action="{{ route('files.delete', $file->id) }}" method="POST" data-confirm="Move this file to trash?" class="m-0" @submit.prevent="submitAction($event)">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-gray-500 hover:text-red-600 rounded hover:bg-dark-card transition-colors" title="Trash">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div
                            class="col-span-full py-8 text-center text-gray-500 border-2 border-dashed border-white/10 rounded-xl">
                            No files found.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Folder Modal -->
    <div x-data="{ show: false }" @open-folder-modal.window="show = true" x-show="show" style="display: none;"
        class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div x-show="show" x-transition.opacity
            class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm transition-opacity" @click="show = false"
            aria-hidden="true"></div>

        <!-- Modal Panel -->
        <div x-show="show" x-transition.scale.origin.center
            class="relative bg-dark-card rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all w-full max-w-lg">
            <form action="{{ route('folders.create') }}" method="POST" @submit.prevent="submitAction($event, () => show = false)">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $parentId }}">
                <div class="px-6 pt-6 pb-4">
                    <h3 class="text-xl font-bold text-white mb-4 text-center">New folder</h3>
                    <input type="text" name="name" required
                        class="w-full border-white/10 rounded-xl focus:border-primary-500 focus:ring focus:ring-primary-200 px-4 py-3"
                        placeholder="Untitled folder">
                </div>
                <div class="px-6 py-4 flex gap-3 justify-center sm:justify-end bg-white/5/50 border-t border-white/10">
                    <button type="button" @click="show = false"
                        class="px-5 py-2.5 rounded-xl font-bold text-gray-200 bg-dark-card border border-gray-300 hover:bg-white/5 transition-colors w-full sm:w-auto">Cancel</button>
                    <button type="submit"
                        class="px-5 py-2.5 rounded-xl font-bold text-dark-bg bg-primary-600 hover:bg-primary-700 transition-colors w-full sm:w-auto">Create</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Rename Modal -->
    <div x-data="{ show: false, itemId: null, itemName: '', itemType: '', actionUrl: '' }" @open-rename-modal.window="
            show = true; 
            itemId = $event.detail.id; 
            itemName = $event.detail.name; 
            itemType = $event.detail.type;
            actionUrl = itemType === 'folder' ? '{{ url('folders') }}/' + itemId : '{{ url('files') }}/' + itemId;
         " x-show="show" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div x-show="show" x-transition.opacity
            class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm transition-opacity" @click="show = false"
            aria-hidden="true"></div>

        <!-- Modal Panel -->
        <div x-show="show" x-transition.scale.origin.center
            class="relative bg-dark-card rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all w-full max-w-lg">
            <form :action="actionUrl" method="POST" @submit.prevent="submitAction($event, () => show = false)">
                @csrf
                @method('PUT')
                <div class="px-6 pt-6 pb-4">
                    <h3 class="text-xl font-bold text-white mb-4 text-center" x-text="'Rename ' + itemType"></h3>
                    <input type="text" name="name" x-model="itemName" required
                        class="w-full border-white/10 rounded-xl focus:border-primary-500 focus:ring focus:ring-primary-200 px-4 py-3">
                </div>
                <div class="px-6 py-4 flex gap-3 justify-center sm:justify-end bg-white/5/50 border-t border-white/10">
                    <button type="button" @click="show = false"
                        class="px-5 py-2.5 rounded-xl font-bold text-gray-200 bg-dark-card border border-gray-300 hover:bg-white/5 transition-colors w-full sm:w-auto">Cancel</button>
                    <button type="submit"
                        class="px-5 py-2.5 rounded-xl font-bold text-dark-bg bg-primary-600 hover:bg-primary-700 transition-colors w-full sm:w-auto">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast Notification -->
    <div x-data="{ toasts: [], addToast(toast) { const id = Date.now() + Math.random(); this.toasts = [...this.toasts, { ...toast, id }]; setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== id); }, 3000); } }" 
         @show-toast.window="addToast($event.detail)"
         class="fixed bottom-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none" style="z-index: 9999;">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-transition.opacity.duration.300ms
                 class="px-4 py-3 rounded-xl shadow-lg border bg-dark-card flex items-center gap-3 w-72 pointer-events-auto"
                 :class="{'border-green-500': toast.type === 'success', 'border-yellow-500': toast.type === 'warning'}">
                <svg x-show="toast.type === 'success'" class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <svg x-show="toast.type === 'warning'" class="w-5 h-5 text-yellow-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <p class="text-sm font-bold text-white" x-text="toast.message"></p>
            </div>
        </template>
    </div>
    </div>
    <script>
        function searchBar() {
            return {
                query: '{{ request('search', '') }}',
                results: { folders: [], files: [] },
                showResults: false,
                loading: false,
                async search() {
                    if (this.query.trim().length < 2) {
                        this.results = { folders: [], files: [] };
                        this.showResults = false;
                        return;
                    }
                    this.loading = true;
                    this.showResults = true;
                    try {
                        let response = await fetch(`{{ route('live-search') }}?query=${encodeURIComponent(this.query)}`);
                        this.results = await response.json();
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.loading = false;
                    }
                },
                highlight(text) {
                    if (!this.query.trim()) return text;
                    const escapedText = text.replace(/[&<>"']/g, m => ({
                        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
                    }[m]));
                    const regex = new RegExp(`(${this.query.trim().replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
                    return escapedText.replace(regex, '<span class="text-primary-500">$1</span>');
                },
                clear() {
                    this.query = '';
                    this.results = { folders: [], files: [] };
                    this.showResults = false;
                }
            }
        }
    </script>
</x-app-layout>