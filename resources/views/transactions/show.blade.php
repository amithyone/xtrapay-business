<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Transaction Details') }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('transactions.edit', $transaction) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Edit Transaction
                </a>
                <a href="{{ route('transactions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Back to Transactions
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Transaction Information</h3>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Reference</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $transaction->reference }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Site</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $transaction->site->name }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Amount</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $transaction->formatted_amount }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $transaction->status_color }}-100 text-{{ $transaction->status_color }}-800">
                                            {{ ucfirst($transaction->status) }}
                                        </span>
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Created At</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $transaction->created_at->format('M d, Y H:i:s') }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Updated At</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $transaction->updated_at->format('M d, Y H:i:s') }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Customer Information</h3>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Customer Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $transaction->customer_name }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Customer Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $transaction->customer_email }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $transaction->description ?: 'No description provided' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    @if($transaction->metadata)
                        <div class="mt-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <pre class="text-sm text-gray-900">{{ json_encode($transaction->metadata, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 