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
                    <flux:button href="{{ route('auth.google') }}">
                        <svg aria-label="Google logo" width="16" height="16" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 512 512">
                            <g>
                                <path d="m0 0H512V512H0" fill="#fff"></path>
                                <path fill="#34a853" d="M153 292c30 82 118 95 171 60h62v48A192 192 0 0190 341"></path>
                                <path fill="#4285f4" d="m386 400a140 175 0 0053-179H260v74h102q-7 37-38 57"></path>
                                <path fill="#fbbc02" d="m90 341a208 200 0 010-171l63 49q-12 37 0 73"></path>
                                <path fill="#ea4335" d="m153 219c22-69 116-109 179-50l55-54c-78-75-230-72-297 55">
                                </path>
                            </g>
                        </svg>
                        Sign In With Google
                    </flux:button>
                </div>
            </form>
        </div>
        <div
            class="hidden md:flex basis-2/5 bg-center bg-cover w-full bg-[url(https://img.freepik.com/premium-vector/realistic-movie-night-advertising-poster-with-popcorn-bucket-3d-glasses-vector-illustration_1284-79161.jpg)] {{ $isSignUp ? 'rounded-s-lg' : 'rounded-e-lg' }} flex-1">
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
