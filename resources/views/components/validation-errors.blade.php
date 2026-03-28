@if ($errors->any())
    <div {{ $attributes }}>
        
        <ul class="list-disc list-inside text-sm text-red-600">
            @foreach ($errors->all() as $error)
                
                @if (!str_contains($error, 'Límite de intentos'))
                    <li>{{ $error }}</li>
                @endif
                
            @endforeach
        </ul>
    </div>
@endif