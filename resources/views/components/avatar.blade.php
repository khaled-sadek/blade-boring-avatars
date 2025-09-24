<div>
    <svg
        viewBox="0 0 36 36"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        width="{{ $size }}"
        height="{{ $size }}"
        role="img"
        @if(isset($title))
            aria-label="{{ $title }}"
        @else
            aria-hidden="true"
        @endif
    >
        @if(isset($title))
            <title>{{ $title }}</title>
        @endif
        @php
            // Define available variants and their mask IDs
            $variants = [
                'beam' => 'beam',
                'marble' => 'marble',
                'pixel' => 'pixel',
                'sunset' => 'sunset',
                'ring' => 'ring',
                'bauhaus' => 'bauhaus'
            ];
            
            // Use the provided variant or default to 'beam' if invalid
            $variant = in_array($variant, array_keys($variants)) ? $variant : 'beam';
            $maskId = 'mask__' . $variant;
        @endphp
        
        <mask
            id="{{ $maskId }}"
            maskUnits="userSpaceOnUse"
            x="0"
            y="0"
            width="36"
            height="36"
        >
            @if($variant === 'beam' || $variant === 'marble')
            <rect
                width="36"
                height="36"
                rx="20"
                fill="white"
            />
            @elseif($variant === 'pixel')
            <rect
                width="36"
                height="36"
                fill="white"
            />
            @else
            <circle
                cx="18"
                cy="18"
                r="18"
                fill="white"
            />
            @endif
        </mask>
        <g mask="url(#{{ $maskId }})" fill="transparent">
            <rect
                width="36"
                height="36"
                rx="20"
                fill="{{ $avatarData['backgroundColor'] }}"
            />
            <rect
                x="0"
                y="0"
                width="36"
                height="36"
                transform="translate({{ $avatarData['wrapperTranslateX'] }} {{ $avatarData['wrapperTranslateY'] }}) rotate({{ $avatarData['wrapperRotate'] }} 18 18) scale({{ $avatarData['wrapperScale'] }})"
                fill="{{ $avatarData['wrapperColor'] }}"
                @if ($avatarData['isCircle'])
                    rx = "36"
                @else
                    rx = "6"
                @endif
            />
            <g
                transform="translate({{ $avatarData['faceTranslateX'] }} {{ $avatarData['faceTranslateY'] }}) rotate({{ $avatarData['faceRotate'] }} 18 18)"
            >
                @if ($avatarData['isMouthOpen'])
                    <path
                        d="M15 {{ 19 + $avatarData['mouthSpread'] }} c2 1 4 1 6 0"
                        stroke="{{ $avatarData['faceColor'] }}"
                        fill="none"
                        strokeLinecap="round"
                    />
                @else
                    <path
                        d="M13 {{ 19 + $avatarData['mouthSpread'] }} a1,0.75 0 0,0 10,0"
                        fill="{{ $avatarData['faceColor'] }}"
                    />
                @endif
                <rect x="{{ 14 - $avatarData['eyeSpread'] }}" y="14" width="1.5" height="2" rx="1" stroke="none" fill="{{ $avatarData['faceColor'] }}" />
                <rect x="{{ 20 + $avatarData['eyeSpread'] }}" y="14" width="1.5" height="2" rx="1" stroke="none" fill="{{ $avatarData['faceColor'] }}" />
            </g>
        </g>
    </svg>
</div>
