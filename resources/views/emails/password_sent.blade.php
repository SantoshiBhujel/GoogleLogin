@component('mail::message')
Hey {{ $user->name }}

Welcome to Google Login

You have successfully been regsitered with your google.

Here goes your password :  {{ $password }}

@component('mail::button', ['url' => ''])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
