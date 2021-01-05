@component('mail::message')
# Introduction

The body of your message.

**{{$username}}** logged in and having status of **{{$status}}**


Thanks,<br>
{{ config('app.name') }}
@endcomponent
