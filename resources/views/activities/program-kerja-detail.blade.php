@extends('layouts.app')

@section('title', $programKerja->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $programKerja->name }}</h1>
                <p class="text-gray-600">Program Kerja Details</p>
                <div class="flex items-center space-x-4 text-sm text-gray-600">
                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $programKerja->status == 'Terlaksana' ? 'bg-green-100 text-green-800' : ($programKerja->status == 'Berjalan' ? 'bg-blue-100 text-blue-800' : ($programKerja->status == 'Direncanakan' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                        {{ $programKerja->status }}
                    </span>
                    <span>Created {{ $programKerja->created_at->format('M j, Y') }}</span>
                    @if($programKerja->updated_at != $programKerja->created_at)
                        <span>Updated {{ $programKerja->updated_at->format('M j, Y') }}</span>
                    @endif
                </div>
            </div>

            @if(auth()->check() && (auth()->id() == $programKerja->created_by || auth()->user()->is_admin))
                <div class="flex space-x-2">
                    <a href="{{ route('program-kerja.edit', $programKerja) }}"
                       class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Edit Program Kerja
                    </a>
                    <form method="POST" action="{{ route('program-kerja.destroy', $programKerja) }}" onsubmit="return confirm('Are you sure you want to delete this program kerja?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Delete
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content & Details -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Image -->
                @if($programKerja->image)
                    <div class="relative h-64 md:h-80 rounded-lg overflow-hidden shadow-lg">
                        <img src="{{ Storage::url($programKerja->image) }}"
                             alt="{{ $programKerja->name }}"
                             class="w-full h-full object-cover">
                    </div>
                @endif

                <!-- Description -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Deskripsi Program Kerja</h2>
                    <div class="prose max-w-none">
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $programKerja->description }}</p>
                    </div>
                </div>

                <!-- Tujuan -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Tujuan Program</h2>
                    @if($programKerja->tujuan && count($programKerja->tujuan) > 0)
                        <ul class="list-disc list-inside space-y-1 text-gray-700">
                            @foreach($programKerja->tujuan as $tujuan)
                                <li>{{ $tujuan }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500">Tujuan belum ditentukan.</p>
                    @endif
                </div>

                <!-- Sasaran Peserta -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Sasaran Peserta</h2>
                    <p class="text-gray-700">{{ $programKerja->sasaran_peserta ?? 'Belum ditentukan' }}</p>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-8">
                <!-- Linimasa -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Linimasa Kegiatan</h2>
                    @if($programKerja->linimasa && count($programKerja->linimasa) > 0)
                        <div class="space-y-4">
                            @foreach($programKerja->linimasa as $item)
                                <div class="relative pl-8">
                                    @if(!$loop->last)
                                        <div class="absolute left-[10px] top-[14px] w-0.5 h-full bg-gray-300"></div>
                                    @endif
                                    <div class="absolute left-0 top-1 flex items-center justify-center w-6 h-6 rounded-full bg-white border-2 border-gray-300">
                                        @if($item['status'] == 'Selesai')
                                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        @else
                                            <div class="w-3 h-3 rounded-full bg-gray-300"></div>
                                        @endif
                                    </div>
                                    <p class="font-semibold {{ $item['status'] == 'Selesai' ? 'text-gray-800' : 'text-gray-600' }}">
                                        {{ $item['description'] }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($item['date'])->format('d F Y') }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">Linimasa belum diatur.</p>
                    @endif
                </div>

                <!-- Anggaran & Kemitraan -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Anggaran & Kemitraan</h2>
                    <div class="space-y-4">
                        @if($programKerja->total_anggaran)
                            <div>
                                <h3 class="font-semibold text-gray-700">Total Anggaran</h3>
                                <p class="text-2xl font-bold text-green-600">
                                    {{ 'Rp ' . number_format($programKerja->total_anggaran, 0, ',', '.') }}
                                </p>
                            </div>
                        @endif

                        @if($programKerja->sumber_dana && count($programKerja->sumber_dana) > 0)
                            <div>
                                <h3 class="font-semibold text-gray-700">Sumber Dana</h3>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    @foreach($programKerja->sumber_dana as $sumber)
                                        <span class="bg-gray-200 text-gray-800 text-xs font-medium px-2 py-1 rounded-full">
                                            {{ $sumber }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($programKerja->mitra_kerja && count($programKerja->mitra_kerja) > 0)
                            <div>
                                <h3 class="font-semibold text-gray-700">Mitra Kerja</h3>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    @foreach($programKerja->mitra_kerja as $mitra)
                                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded-full">
                                            {{ $mitra }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Organization -->
                @if($programKerja->organization)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Diselenggarakan Oleh</h2>
                        <div class="flex flex-col items-center text-center p-2 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">
                            @if($programKerja->organization->logo)
                                <img src="{{ Storage::url($programKerja->organization->logo) }}"
                                     alt="{{ $programKerja->organization->name }}"
                                     class="w-16 h-16 object-cover rounded-full border-2 border-[--primary-blue] p-1">
                            @else
                                <div class="w-16 h-16 bg-gradient-to-br from-[--primary-blue] to-blue-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-xl font-bold">{{ substr($programKerja->organization->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <h4 class="text-md font-bold font-lora text-[--primary-blue] mt-2">
                                {{ $programKerja->organization->name }}
                            </h4>
                            <p class="text-[--text-secondary] text-sm">
                                {{ $programKerja->organization->faculty }}
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection