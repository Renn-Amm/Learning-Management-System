@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-black focus:border-black focus:ring-black rounded-md shadow-sm']) }}>
