@extends("layout")

@section("title", $title)

@section("content")
<main class="flex flex-col items-start justify-start w-full" x-data="{
    productModal: false,
    products: {{  Illuminate\Support\Js::from($products) }},
    categories: {{  Illuminate\Support\Js::from($categories) }},
    total_payment:0,
    items: [],
    addItem(product) {
        const existingIndex = this.items.findIndex(item => item.product.id == product.id);

        if(existingIndex !== -1) {
            let quantity = this.items[existingIndex].quantity;
            let total = this.items[existingIndex].total;

            quantity += 1;
            total = quantity * this.items[existingIndex].product.price_sell;

            this.items[existingIndex].quantity = quantity;
            this.items[existingIndex].total = total;
            this.productModal = false;
        } else {
            this.items.push({
                product: product,
                quantity: 1,
                discount: 0,
                total: 1 * product.price_sell
            });
             this.productModal = false;
        }

    },
    increament(index){
        let quantity = this.items[index].quantity;
        let total = this.items[index].total;

        quantity += 1;
        total = quantity * this.items[index].product.price_sell;

        this.items[index].quantity = quantity;
        this.items[index].total = total;
    },
    decreament(index){
        let quantity = this.items[index].quantity;
        let total = this.items[index].total;
        let decreament = quantity -= 1;

        if(decreament == 0) {
            this.items.splice(index, 1);
        } else {
            total = quantity * this.items[index].product.price_sell;
            this.items[index].quantity = decreament;
            this.items[index].total = total;
        }
    },
    removeItem(index) {
        this.items.splice(index, 1);
    },
    get grandTotal() {
        let items = this.items;
        let grandTotal = items.length == 0 ? 0 : items.length == 1? items[0].total - items[0].discount : items.reduce((acc, item) => acc + (item.total - item.discount), 0);
        this.total_payment = grandTotal - this.$refs.discount.value;
        return grandTotal;
    },
    get totalItem() {
        return this.items.length;
    },
    getDiscount() {
        const discount = this.$refs.discount.value;
        const discount_value = this.grandTotal - discount;
        this.total_payment = discount_value;
    }
}">
    <a href="{{ route('transaction.index') }}" class="bg-pink-500 text-white px-6 py-3 mb-4 rounded-lg shadow hover:bg-pink-600 transition-colors font-semibold">
        Kembali
    </a>
    <form action="{{ route('transaction.store') }}" method="post" class="grid grid-cols-6 gap-4 w-full">
        @csrf

        <section x-show="items.length == 0" class="col-span-4 flex justify-center items-center h-fit p-6 border-2 border-dashed border-pink-300 bg-pink-100 rounded-xl text-pink-500">
            Belum ada produk ditambahkan
        </section>

        <section x-show="items.length > 0" class="col-span-4 flex flex-col gap-y-3">
            <template x-for="(item, index) in items" :key="index + '-item'">
                <div
                    class="w-full flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-xl shadow-md bg-white border border-pink-200">

                    <!-- Info Produk -->
                    <div class="flex flex-col flex-1">
                        <p class="text-base font-bold text-gray-800" x-text="item.product.name"></p>
                        <p class="text-sm text-gray-600">
                            <span x-text="item.quantity"></span> ×
                            Rp. <span x-text="item.product.price_sell.toLocaleString()"></span>
                        </p>
                        <p class="text-sm font-semibold text-pink-600 mt-1"
                            x-text="'Subtotal: Rp. ' + (item.quantity * item.product.price_sell).toLocaleString()">
                        </p>
                        <p class="text-sm font-semibold text-red-600 mt-1"
                            x-text="'Diskon: Rp. ' + (item.discount ?? 0).toLocaleString()">
                        </p>
                        <p class="text-sm font-semibold text-pink-600 mt-1"
                            x-text="'Total: Rp. ' + (item.quantity * item.product.price_sell - (item.discount ?? 0)).toLocaleString()">
                        </p>
                    </div>

                    <!-- Input Discount -->
                    <div class="flex flex-col">
                        <label class="text-xs font-medium text-gray-500 mb-1">Discount</label>
                        <input type="number" min="0"
                            class="w-28 rounded-lg border-gray-300 px-2 py-1 focus:border-pink-400 focus:ring focus:ring-pink-200 text-sm"
                            x-model.number="item.discount"
                            :name="'items[' + index + '][discount]'"
                            placeholder="0" />
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex items-center rounded-xl overflow-hidden">
                        <button @click="increament(index)" type="button"
                            class="px-3 py-2 bg-green-500 text-white hover:bg-green-600 transition">
                            +
                        </button>
                        <button @click="decreament(index)" type="button"
                            class="px-3 py-2 bg-amber-500 text-white hover:bg-amber-600 transition">
                            −
                        </button>
                        <button @click="removeItem(index)" type="button"
                            class="px-3 py-2 bg-red-500 text-white hover:bg-red-600 transition">
                            🗑️
                        </button>
                    </div>

                    <!-- Hidden Input -->
                    <template x-for="(field, key) in {
                product_id: item.product.id,
                quantity: item.quantity,
                price: item.product.price_sell,
                discount: item.discount ?? 0
            }" :key="key">
                        <input type="hidden" :name="'items[' + index + '][' + key + ']'" :value="field" />
                    </template>
                </div>
            </template>
        </section>

        <section class="col-span-2 h-fit p-6 rounded-xl shadow-lg bg-white border border-pink-200">
            <button type="button" @click="productModal = true" class="w-full bg-pink-500 text-sm text-white px-6 py-3 mb-4 rounded-lg shadow hover:bg-pink-600 transition-colors font-semibold cursor-pointer">
                Tambah Produk
            </button>

            <div class="mb-4">
                <select id="member_id" name="member_id" class="w-full px-3 py-2 border border-gray-300 rounded">
                    <option value="" disabled {{ old('member_id') ? '' : 'selected' }}>Pilih Member</option>
                    @foreach ($members as $member)
                    <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>
                        {{ $member->name }}
                    </option>
                    @endforeach
                </select>
                @error('member_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @else
                <p class="ms-2 text-blue-500 text-sm mt-1">Kosongkan jika bukan member</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="total_item" class="block text-gray-700 font-bold mb-2">Total Item:</label>
                <input type="text" id="total_item" x-model="totalItem" name="total_item" value="{{ old('total_item') }}" class="w-full px-3 py-2 border border-gray-300 rounded" readonly>
            </div>

            <div class="mb-4">
                <label for="total" class="block text-gray-700 font-bold mb-2">Total Rp. :</label>
                <input type="number" id="total" x-model="grandTotal" class="w-full px-3 py-2 border border-gray-300 rounded" readonly>
            </div>

            <div class="mb-4">
                <label for="discount" class="block text-gray-700 font-bold mb-2">Diskon:</label>
                <input type="number" id="discount" x-ref="discount" name="discount" value="{{ old('discount') }}" class="w-full px-3 py-2 border border-gray-300 rounded" x-on:change="getDiscount()">
                @error('discount')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="total_price" class="block text-gray-700 font-bold mb-2">Grand Total Rp. :</label>
                <input type="number" id="total_price" x-model="total_payment" name="total_price" value="{{ old('total_price') }}" class="w-full px-3 py-2 border border-gray-300 rounded" readonly>
            </div>

            <div class="flex flex-col w-fit mb-4">
                <label class="text-sm text-gray-700 mb-1">Status Pembayaran</label>
                <div class="flex items-center space-x-4">
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="is_paid" value="1" {{ old('is_paid') == '1' ? 'checked' : '' }} class="form-radio text-pink-600">
                        <span>Dibayar</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="is_paid" value="0" {{ old('is_paid') == '0' ? 'checked' : '' }} class="form-radio text-pink-600">
                        <span>Belum Dibayar</span>
                    </label>
                </div>
                @error('is_paid')
                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <button @click="loading = true" class="w-full bg-pink-500 text-sm text-white px-6 py-3 mb-4 rounded-lg shadow hover:bg-pink-600 transition-colors font-semibold cursor-pointer">
                Simpan
            </button>
        </section>
    </form>

    <div
        x-data="{
            data: products,
            filtered: 'semua',
            get filteredProduct() {
                const initialData = products;

                if(this.filtered === 'semua') {
                    return initialData;
                } else {
                    return this.data.filter(p => p.category_id == this.filtered)
                }
            }
        }"
        x-show="productModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="display: none;"
        class="fixed inset-0 z-50 flex items-start justify-center bg-black/50 bg-opacity-40">
        <div class="relative max-w-4xl w-full mt-16 p-6 rounded-xl shadow-lg bg-white border border-pink-200">
            <button class="absolute -top-4 -right-4 p-2 rounded-full bg-pink-500 text-white border border-pink-500 hover:bg-pink-50 hover:text-pink-500 transition" @click="productModal = false">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                    <path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                </svg>
            </button>
            <div class="inline-flex justify-center gap-x-4 mb-4 w-full">
                <button @click="filtered = 'semua'" type="button" class="outline-0 px-4 py-2 rounded-full  transition cursor-pointer" :class="{'bg-pink-500 border border-pink-500 hover:bg-pink-700 hover:text-white':filtered == 'semua', 'bg-pink-50 border border-pink-500 hover:bg-pink-500 hover:text-white':filtered != 'semua'}">Semua</button>
                <template x-for="(category, index) in categories" :key="'category-' + index">
                    <button @click="filtered = category.id" type="button" class="outline-0 px-4 py-2 rounded-full bg-pink-50 border border-pink-500 hover:bg-pink-500 hover:text-white transition cursor-pointer" x-text="category.category" :class="{'bg-pink-500 border border-pink-500 hover:bg-pink-700 hover:text-white':filtered == category.id, 'bg-pink-50 border border-pink-500 hover:bg-pink-500 hover:text-white':filtered != category.id}"></button>
                </template>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <template x-for="product in filteredProduct" :key="product.id">
                    <button type="button" @click="addItem(product)" class="inline-flex items-center justify-between p-4 rounded-xl border border-pink-300 bg-pink-50 shadow-lg hover:bg-pink-500 hover:text-white hover:shadow-md transition">
                        <span class="text-sm" x-text="product.name"></span>
                        <span class="text-sm" x-text="product.price_sell"></span>
                    </button>
                </template>
            </div>
        </div>
    </div>
</main>
@endsection
