@extends("layout")

@section("title", $title)

@section("content")
<main>
    <section class="flex flex-col items-start justify-start p-4 rounded-lg shadow-md bg-white border border-pink-200">
        <a href="{{ route('items-out.index') }}" class="bg-pink-500 text-white px-4 py-2 mb-4 rounded hover:bg-pink-600">Kembali</a>
        <form action="{{ route('items-out.store') }}" method="POST" class="w-full" x-data="{
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
        addItem() {
            const productId = this.$refs.product_id.value;
            const product = this.products.find(p => p.id == productId);
            const quantity = parseInt(this.$refs.quantity.value);
            const note = this.$refs.note.value;

            if (!productId || !quantity) return;

            if (product.quantity < quantity) {
                alert('Tidak bisa mengurangi stock. Jumlah Stock ' + product.name + ' tersisa : ' + product.quantity);
                return;
            }

            const existingIndex = this.items.findIndex(item => item.product_id == productId);

            if (existingIndex !== -1) {
                // Update quantity if product already exists
                this.items[existingIndex].quantity += quantity;
            } else {
                this.items.push({
                    product_id: productId,
                    product: product,
                    quantity: quantity,
                    note: note,
                });
            }

            this.$refs.product_id.value = '';
            this.$refs.quantity.value = '';
            this.$refs.note.value = '';
        },
        editItem(index) {
            const item = this.items[index];
            this.$refs.product_id.value = item.product_id;
            this.$refs.quantity.value = item.quantity;
            this.$refs.note.value = item.note;
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
                <div class="flex flex-col w-fit">
                    <label class="text-sm text-gray-700 mb-1">Status Barang</label>
                    <div class="flex items-center space-x-4">
                        @foreach($statusEnum as $enum)
                        <label class="flex items-center space-x-2">
                            <input type="radio" name="status" value="{{ $enum->value }}" {{ old('status') == $enum->value ? 'checked' : '' }} class="form-radio text-pink-600">
                            <span>{{ $enum->name }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('status')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="bg-pink-500 text-white px-4 py-2 rounded hover:bg-pink-600">Simpan</button>
            </div>

            <div class="inline-flex justify-between items-start w-full pb-4 mb-4 space-x-4 border-b border-pink-300">
                <select x-ref="product_id" class="flex-2 w-full max-w-lg p-2 border border-pink-300 rounded">
                    <option value="">Pilih Produk</option>
                    <template x-for="product in products" :key="product.id">
                        <option :value="product.id" x-text="product.name + ' - ' + product.brand + ' (Stok : ' + product.quantity + ')'"></option>
                    </template>
                </select>

                <input type="number" x-ref="quantity" placeholder="Jumlah" class="flex-1 w-full max-w-lg p-2 border border-pink-300 rounded" />

                <input type="text" x-ref="note" placeholder="Catatan..." class="flex-1 w-full max-w-lg p-2 border border-pink-300 rounded" />

                <button @click="addItem()" type="button" id="add-item" class="bg-pink-500 text-white px-4 py-2 rounded hover:bg-pink-600">Tambah</button>
            </div>

            <table class="w-full border-collapse mb-4">
                <thead>
                    <tr>
                        <th class="border border-pink-300 p-2 text-left">Produk</th>
                        <th class="border border-pink-300 p-2 text-left">Jumlah</th>
                        <th class="border border-pink-300 p-2 text-left">Catatan</th>
                        <th class="border border-pink-300 p-2 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr x-show="items.length === 0">
                        <td colspan="4" class="border border-pink-300 p-2 text-center">Tidak ada produk yang ditambahkan.</td>
                    </tr>
                    <template x-for="(item, index) in items" :key="index">
                        <tr>
                            <td class="border border-pink-300 p-2" x-text="item.product.name"></td>
                            <td class="border border-pink-300 p-2" x-text="item.quantity"></td>
                            <td class="border border-pink-300 p-2" x-text="item.note"></td>
                            <td class="border border-pink-300 p-2 w-fit">
                                <button @click="editItem(index)" type="button" class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600 mr-2">Edit</button>
                                <button @click="removeItem(index)" type="button" class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">Hapus</button>
                            </td>

                            <input type="hidden" :name="'items[' + index + '][product_id]'" :value="item.product_id" />
                            <input type="hidden" :name="'items[' + index + '][quantity]'" :value="item.quantity" />
                            <input type="hidden" :name="'items[' + index + '][note]'" :value="item.note" />
                        </tr>
                    </template>
                </tbody>
            </table>

            <div class="flex flex-col w-full">
                <textarea
                    name="note"
                    placeholder="Catatan..."
                    class="flex-1 w-full p-2 border border-pink-300 rounded resize-none"
                    rows="3"></textarea>
                @error('note')
                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
        </form>
    </section>
</main>
@endsection
