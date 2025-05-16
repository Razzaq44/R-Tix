<div class="flex flex-col justify-center items-center w-full h-full">
    @if (session('error'))
        <div class="w-full">
            <flux:callout variant="danger" color="rose" icon="x-circle" heading="{{ session('error') }}" class="mb-8" />
        </div>
    @endif
    <div
        class="bg-white dark:bg-white/5 rounded-lg md:flex md:w-[700px] shadow-xl {{ $isSignUp ? 'flex-row-reverse' : 'flex-row' }} h-fit">
        <div class="md:basis-3/5 p-8 md:pr-12 w-full h-full">
            <form wire:submit.prevent="" class="flex flex-col gap-6">
                <div class="">
                    <flux:heading size="xl">{{ $isSignUp ? 'Sign Up' : 'Sign In' }}</flux:heading>
                    <flux:text>
                        {{ $isSignUp ? 'Create an account to get started!' : 'Let\'s get you set up and ready to go! 🚀' }}
                    </flux:text>
                </div>
                <div class="flex-col gap-4 {{ $isSignUp ? 'hidden' : 'flex' }}">
                    <flux:input label="Email or username" type="text" wire:model.lazy="login" />
                    <div class="flex justify-between">
                        <flux:label>Password</flux:label>
                        <flux:label>
                            <flux:button href="/forgot-password" variant="ghost" wire:navigate
                                class="text-accent! p-0! hover:bg-transparent! hover:underline">
                                Forgot Password?
                            </flux:button>
                        </flux:label>
                    </div>
                    <flux:input type="password" viewable wire:model.lazy="password" />
                    <flux:field variant="inline">
                        <flux:checkbox wire:model="remember" />
                        <flux:label>Remember me</flux:label>
                    </flux:field>
                </div>
                <div class="flex-col gap-4 {{ $isSignUp ? 'flex' : 'hidden' }}">
                    <flux:input label="Email" type="text" wire:model.lazy="email" />
                    <flux:input label="Username" type="text" wire:model.lazy="name" />
                    <flux:input type="password" label="Password" wire:model.lazy="password" viewable />
                    <flux:input type="password" label="Password Confirmation" wire:model.lazy="password_confirmation"
                        viewable />
                </div>
                <div class="flex flex-col gap-4">
                    @if ($isSignUp)
                        <flux:button type="submit" variant="primary" wire:click="signUp">
                            Sign Up</flux:button>
                    @else
                        <flux:button type="submit" variant="primary" wire:click="signIn">
                            Sign In</flux:button>
                    @endif
                    <flux:separator text="or" variant="subtle" />
                    <flux:button href="{{ route('auth.google') }}">Sign In With Google</flux:button>
                </div>
            </form>
        </div>
        <div
            class="hidden md:flex basis-2/5 bg-center bg-cover w-full bg-[url(https://img.freepik.com/free-photo/low-angle-view-business-buildings-with-plane-flying_1359-480.jpg?t=st=1746523701~exp=1746527301~hmac=d7af6926c07d16675cf2e0a1ee1af1c621d2a8df32587325f9a625af10b2b629&w=900)] {{ $isSignUp ? 'rounded-s-lg' : 'rounded-e-lg' }} flex-1">
            <div class="flex flex-col justify-end w-full p-8 items-center py-16">
                <flux:button class="w-full underline" variant="ghost"
                    wire:click="{{ $isSignUp ? 'showSignIn' : 'showSignUp' }}">{{ $isSignUp ? 'Sign In' : 'Sign Up' }}
                    Here!</flux:button>
            </div>
        </div>
    </div>
    <flux:button class="mt-8 hover:bg-transparent!" onclick="window.history.back();" variant="ghost" wire:navigate>Back
    </flux:button>
</div>
