<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Transaction') }}
            </h2>
            <a href="{{ route('transactions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                Back to Transactions
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('transactions.update', $transaction) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-label for="site_id" :value="__('Site')" />
                            <select id="site_id" name="site_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="">Select a site</option>
                                @foreach(Auth::user()->businessProfile->sites as $site)
                                    <option value="{{ $site->id }}" {{ (old('site_id', $transaction->site_id) == $site->id) ? 'selected' : '' }}>
                                        {{ $site->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('site_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="amount" :value="__('Amount')" />
                            <x-input id="amount" class="block mt-1 w-full" type="number" name="amount" :value="old('amount', $transaction->amount)" step="0.01" required />
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="currency" :value="__('Currency')" />
                            <select id="currency" name="currency" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="NGN" {{ old('currency', $transaction->currency) == 'NGN' ? 'selected' : '' }}>Nigerian Naira (NGN)</option>
                                <option value="USD" {{ old('currency', $transaction->currency) == 'USD' ? 'selected' : '' }}>US Dollar (USD)</option>
                                <option value="EUR" {{ old('currency', $transaction->currency) == 'EUR' ? 'selected' : '' }}>Euro (EUR)</option>
                                <option value="GBP" {{ old('currency', $transaction->currency) == 'GBP' ? 'selected' : '' }}>British Pound (GBP)</option>
                            </select>
                            <x-input-error :messages="$errors->get('currency')" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="pending" {{ old('status', $transaction->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="success" {{ old('status', $transaction->status) == 'success' ? 'selected' : '' }}>Success</option>
                                <option value="failed" {{ old('status', $transaction->status) == 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="payment_method" :value="__('Payment Method')" />
                            <select id="payment_method" name="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="card" {{ old('payment_method', $transaction->payment_method) == 'card' ? 'selected' : '' }}>Card</option>
                                <option value="bank_transfer" {{ old('payment_method', $transaction->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="ussd" {{ old('payment_method', $transaction->payment_method) == 'ussd' ? 'selected' : '' }}>USSD</option>
                            </select>
                            <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="customer_email" :value="__('Customer Email')" />
                            <x-input id="customer_email" class="block mt-1 w-full" type="email" name="customer_email" :value="old('customer_email', $transaction->customer_email)" required />
                            <x-input-error :messages="$errors->get('customer_email')" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="customer_name" :value="__('Customer Name')" />
                            <x-input id="customer_name" class="block mt-1 w-full" type="text" name="customer_name" :value="old('customer_name', $transaction->customer_name)" required />
                            <x-input-error :messages="$errors->get('customer_name')" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="3">{{ old('description', $transaction->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-button class="ml-4">
                                {{ __('Update Transaction') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 