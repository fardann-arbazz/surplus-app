<div x-show="cartOpen" @click="cartOpen = false" class="fixed inset-0 bg-black/50 z-40 lg:hidden" x-transition.opacity>
</div>

<div x-show="cartOpen" class="fixed inset-y-0 right-0 w-full sm:w-96 bg-base-100 shadow-2xl z-50 drawer drawer-end"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full"
    x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
    <div class="h-full flex flex-col">
        <!-- Cart Header -->
        <div class="p-4 border-b border-base-200 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold">Your Cart</h3>
                <p class="text-sm text-base-content/60" x-text="cartCount + ' items'"></p>
            </div>
            <button @click="cartOpen = false" class="btn btn-ghost btn-sm btn-circle">✕</button>
        </div>

        <!-- Cart Items -->
        <div class="flex-1 overflow-y-auto p-4 space-y-3">
            <template x-for="(item, index) in cart" :key="index">
                <div class="flex gap-3 bg-base-200/50 rounded-xl p-3">
                    <img :src="item.image" :alt="item.name"
                        class="w-16 h-16 rounded-xl object-cover flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold truncate" x-text="item.name"></h4>
                        <p class="text-xs text-base-content/50" x-text="item.restaurant"></p>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-sm font-bold text-warning" x-text="item.price"></span>
                            <div class="join">
                                <button @click="decreaseQty(index)" class="join-item btn btn-xs btn-outline">−</button>
                                <span class="join-item btn btn-xs btn-outline no-animation pointer-events-none"
                                    x-text="item.qty"></span>
                                <button @click="increaseQty(index)" class="join-item btn btn-xs btn-outline">+</button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <div x-show="cart.length === 0" class="text-center py-12">
                <span class="text-5xl mb-4 block">🛒</span>
                <p class="text-base-content/60 text-sm">Your cart is empty</p>
                <p class="text-base-content/40 text-xs mt-1">Add items to get started</p>
            </div>
        </div>

        <!-- Cart Footer -->
        <div x-show="cart.length > 0" class="p-4 border-t border-base-200 bg-base-100">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-base-content/60">Total</span>
                <span class="text-lg font-bold" x-text="'Rp ' + totalPrice.toLocaleString()"></span>
            </div>
            <button class="btn btn-warning w-full">
                Checkout Now
            </button>
        </div>
    </div>
</div>
