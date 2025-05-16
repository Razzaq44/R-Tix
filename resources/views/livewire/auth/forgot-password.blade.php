<div class="flex flex-col justify-center items-center w-full h-full">
    @if (session('error'))
        <div class="w-full">
            <flux:callout variant="danger" color="rose" icon="x-circle" heading="{{ session('error') }}" class="mb-8" />
        </div>
    @endif
    <div class="bg-white dark:bg-white/5 rounded-lg md:flex md:w-[700px] shadow-xl flex-row h-fit">
        <div class="md:basis-3/5 p-8 md:pr-12 w-full h-full">
            <form wire:submit.prevent="" class="flex flex-col gap-6">
                <div class="">
                    <flux:heading size="xl">Reset Your Password</flux:heading>
                    <flux:text>
                        Let's get you set up and ready to go! 🚀
                    </flux:text>
                </div>
                <div class="flex-col gap-4 flex">
                    <flux:input label="Email" type="text" wire:model.lazy="email" />
                </div>
                <div class="flex flex-col gap-4">
                    <flux:button type="submit" variant="primary" wire:click="sendResetPasswordLink">
                        Send Reset Password Link</flux:button>
                </div>
            </form>
        </div>
        <div
            class="hidden md:flex basis-2/5 bg-center bg-cover w-full bg-[url(https://img.freepik.com/free-photo/low-angle-view-business-buildings-with-plane-flying_1359-480.jpg?t=st=1746523701~exp=1746527301~hmac=d7af6926c07d16675cf2e0a1ee1af1c621d2a8df32587325f9a625af10b2b629&w=900)] rounded-e-lg flex-1">
        </div>
    </div>
    <flux:button class="mt-8 hover:bg-transparent!" onclick="window.history.back();" variant="ghost" wire:navigate>Back
    </flux:button>
</div>
