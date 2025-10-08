@extends("layout")

@section("title", $title)

@section("content")
<main>
    <section class="flex flex-col items-start justify-start p-4 rounded-lg shadow-md bg-white border border-pink-200">
        <a href="{{ route('items-in.index') }}" class="bg-pink-500 text-white px-4 py-2 mb-4 rounded hover:bg-pink-600">Kembali</a>
        <form action="{{ route('items-in.store') }}" method="POST" class="w-full" x-data="{
        products: {{ Illuminate\Support\Js::from($products) }},
        items: [],
        submit() {
            if(this.items.length === 0) {
                alert('Tambahkan setidaknya satu produk.');
                return;
            }
            loading = true;
            this.$el.submit();
        },
        selectedProduct(){
            this.$refs.price.value = this.products.find(p => p.id == this.$refs.product_id.value).price_buy;
        },
        addItem() {
            const productId = this.$refs.product_id.value;
            const quantity = parseInt(this.$refs.quantity.value);
            const price = parseFloat(this.$refs.price.value);

            if (!productId || !quantity || !price) return;

            const existingIndex = this.items.findIndex(item => item.product_id == productId);

            if (existingIndex !== -1) {
                // Update quantity if product already exists
                this.items[existingIndex].quantity += quantity;
                this.items[existingIndex].price = price; // Optionally update price
            } else {
                this.items.push({
                    product_id: productId,
                    product: this.products.find(p => p.id == productId),
                    quantity: quantity,
                    price: price
                });
            }

            this.$refs.product_id.value = '';
            this.$refs.quantity.value = '';
            this.$refs.price.value = '';
        },
        editItem(index) {
            const item = this.items[index];
            this.$refs.product_id.value = item.product_id;
            this.$refs.quantity.value = item.quantity;
            this.$refs.price.value = item.price;
            this.items.splice(index, 1);
        },
        removeItem(index) {
            this.items.splice(index, 1);
        },
        total() {
            return this.items.reduce((sum, item) => sum + (item.quantity * item.price), 0);
        }
        }" @submit.prevent="submit()">
            @csrf
            <div class="inline-flex justify-between items-center pb-4 mb-4 w-full space-x-4 border-b border-pink-300">
                <div class="flex flex-col w-full max-w-lg">
                    <select name="supplier_id" class="p-2 border border-pink-300 rounded">
                        <option value="">Pilih Supplier</option>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex flex-col w-fit">
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

                <button type="submit" class="bg-pink-500 text-white px-4 py-2 rounded hover:bg-pink-600">Simpan</button>
            </div>

            <div class="inline-flex justify-between items-start w-full pb-4 mb-4 space-x-4 border-b border-pink-300">
                <select x-ref="product_id" x-on:change="selectedProduct()" class="flex-2 w-full max-w-lg p-2 border border-pink-300 rounded">
                    <option value="">Pilih Produk</option>
                    <template x-for="product in products" :key="product.id">
                        <option :value="product.id" x-text="product.name"></option>
                    </template>
                </select>

                <input type="number" x-ref="quantity" placeholder="Jumlah" class="flex-1 w-full max-w-lg p-2 border border-pink-300 rounded" />

                <input type="number" x-ref="price" placeholder="Harga" class="flex-1 w-full max-w-lg p-2 border border-pink-300 rounded" />

                <button @click="addItem()" type="button" id="add-item" class="bg-pink-500 text-white px-4 py-2 rounded hover:bg-pink-600">Tambah</button>
            </div>

            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="border border-pink-300 p-2 text-left">Produk</th>
                        <th class="border border-pink-300 p-2 text-left">Jumlah</th>
                        <th class="border border-pink-300 p-2 text-left">Harga</th>
                        <th class="border border-pink-300 p-2 text-left">Subtotal</th>
                        <th class="border border-pink-300 p-2 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr x-show="items.length === 0">
                        <td colspan="5" class="border border-pink-300 p-2 text-center">Tidak ada produk yang ditambahkan.</td>
                    </tr>
                    <template x-for="(item, index) in items" :key="index">
                        <tr>
                            <td class="border border-pink-300 p-2" x-text="item.product.name"></td>
                            <td class="border border-pink-300 p-2" x-text="item.quantity"></td>
                            <td class="border border-pink-300 p-2" x-text="item.price.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' })"></td>
                            <td class="border border-pink-300 p-2" x-text="(item.quantity * item.price).toLocaleString('id-ID', { style: 'currency', currency: 'IDR' })"></td>
                            <td class="border border-pink-300 p-2">
                                <button @click="editItem(index)" type="button" class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600 mr-2">Edit</button>
                                <button @click="removeItem(index)" type="button" class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">Hapus</button>
                            </td>

                            <input type="hidden" :name="'items[' + index + '][product_id]'" :value="item.product_id" />
                            <input type="hidden" :name="'items[' + index + '][quantity]'" :value="item.quantity" />
                            <input type="hidden" :name="'items[' + index + '][price]'" :value="item.price" />
                        </tr>
                    </template>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="border border-pink-300 p-2 text-right font-bold">Total</td>
                        <td colspan="2" class="border border-pink-300 p-2 font-bold" x-text="total().toLocaleString('id-ID', { style: 'currency', currency: 'IDR' })"></td>
                    </tr>
                </tfoot>
            </table>
        </form>
    </section>
</main>
@endsection
