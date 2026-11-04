@props(['user', 'size' => 'size-8'])

{{-- DiceBear Waves, seeded by the user id so the picture never changes when the name or email does --}}
<span {{ $attributes->class(['avatar inline-block shrink-0 overflow-hidden rounded-full bg-fog', $size]) }} role="img" aria-label="{{ __('Avatar of :name', ['name' => $user->name]) }}">{!! \App\Support\Avatars::svg('user-'.$user->id) !!}</span>
