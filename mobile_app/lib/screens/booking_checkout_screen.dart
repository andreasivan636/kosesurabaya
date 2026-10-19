import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../config/constants.dart';
import '../models/kos_model.dart';
import '../models/tipe_kamar_model.dart';
import '../services/api_service.dart';
import 'booking_success_screen.dart';

class BookingCheckoutScreen extends StatefulWidget {
  final KosModel kos;
  final TipeKamarModel tipeKamar;

  const BookingCheckoutScreen({
    super.key,
    required this.kos,
    required this.tipeKamar,
  });

  @override
  State<BookingCheckoutScreen> createState() => _BookingCheckoutScreenState();
}

class _BookingCheckoutScreenState extends State<BookingCheckoutScreen> {
  String _durasi = 'bulanan'; // 'bulanan' or 'tahunan'
  DateTime _tanggalMulai = DateTime.now().add(const Duration(days: 1));
  final TextEditingController _catatanController = TextEditingController();
  bool _isLoading = false;

  int get _totalBayar {
    if (_durasi == 'tahunan' && widget.tipeKamar.hargaTahunan != null) {
      return widget.tipeKamar.hargaTahunan!;
    }
    return widget.tipeKamar.hargaBulanan;
  }

  Future<void> _processPayment() async {
    setState(() => _isLoading = true);

    final res = await ApiService().createBooking(
      tipeKamarId: widget.tipeKamar.id,
      durasi: _durasi,
      tanggalMulai: DateFormat('yyyy-MM-dd').format(_tanggalMulai),
      catatan: _catatanController.text.trim(),
    );

    setState(() => _isLoading = false);

    if (res['success'] == true && mounted) {
      final snapToken = res['snap_token'];
      final orderId = res['order_id'];

      // Show payment confirmation & simulated/snap checkout
      showModalBottomSheet(
        context: context,
        isScrollControlled: true,
        backgroundColor: AppConstants.darkBg,
        shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
        builder: (ctx) => Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                width: 50,
                height: 50,
                decoration: BoxDecoration(
                  color: AppConstants.primary.withOpacity(0.2),
                  shape: BoxShape.circle,
                ),
                child: const Center(
                  child: Icon(Icons.payment_rounded, color: AppConstants.primary, size: 28),
                ),
              ),
              const SizedBox(height: 16),
              const Text(
                'Midtrans Payment Gateway',
                style: TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 8),
              Text(
                'Order ID: $orderId',
                style: const TextStyle(color: Colors.white54, fontSize: 12),
              ),
              const SizedBox(height: 16),
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: AppConstants.darkCard,
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Column(
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.between,
                      children: [
                        const Text('Total Pembayaran', style: TextStyle(color: Colors.white70, fontSize: 13)),
                        Text(
                          NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0).format(_totalBayar),
                          style: const TextStyle(color: AppConstants.primaryLight, fontWeight: FontWeight.bold, fontSize: 16),
                        ),
                      ],
                    ),
                    const Divider(color: Colors.white12, height: 24),
                    const Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text('Metode Pembayaran', style: TextStyle(color: Colors.white70, fontSize: 13)),
                        Text('QRIS / VA / GoPay', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 13)),
                      ],
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 24),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppConstants.primary,
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                  ),
                  onPressed: () {
                    Navigator.pop(ctx);
                    Navigator.pushReplacement(
                      context,
                      MaterialPageRoute(
                        builder: (_) => BookingSuccessScreen(
                          orderId: orderId,
                          kosName: widget.kos.namaKos,
                          tipeKamarName: widget.tipeKamar.namaTipe,
                          totalBayar: _totalBayar,
                          tanggalMulai: _tanggalMulai,
                          durasi: _durasi,
                        ),
                      ),
                    );
                  },
                  child: const Text('Simulasikan Bayar Berhasil (Sandbox)', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                ),
              ),
            ],
          ),
        ),
      );
    } else if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(res['message'] ?? 'Gagal membuat booking')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final currencyFormatter = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);

    return Scaffold(
      backgroundColor: AppConstants.darkBg,
      appBar: AppBar(
        backgroundColor: AppConstants.darkBg,
        elevation: 0,
        title: const Text('Konfirmasi Booking', style: TextStyle(color: Colors.white, fontSize: 17, fontWeight: FontWeight.bold)),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Kos & Room Summary
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: AppConstants.darkCard,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: Colors.white12),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    widget.kos.namaKos,
                    style: const TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    widget.kos.alamat,
                    style: const TextStyle(color: Colors.white54, fontSize: 12),
                  ),
                  const Divider(color: Colors.white12, height: 20),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.between,
                    children: [
                      Text(
                        'Kamar: Tipe ${widget.tipeKamar.tipe} (${widget.tipeKamar.namaTipe})',
                        style: const TextStyle(color: AppConstants.accent, fontWeight: FontWeight.bold, fontSize: 13),
                      ),
                      Text(
                        '${currencyFormatter.format(widget.tipeKamar.hargaBulanan)}/bln',
                        style: const TextStyle(color: AppConstants.primaryLight, fontWeight: FontWeight.bold, fontSize: 13),
                      ),
                    ],
                  ),
                ],
              ),
            ),
            const SizedBox(height: 20),

            // Durasi Sewa
            const Text('Durasi Sewa', style: TextStyle(color: Colors.white, fontSize: 14, fontWeight: FontWeight.bold)),
            const SizedBox(height: 8),
            Row(
              children: [
                Expanded(
                  child: ChoiceChip(
                    label: const Center(child: Text('Bulanan (1 Bulan)')),
                    selected: _durasi == 'bulanan',
                    onSelected: (_) => setState(() => _durasi = 'bulanan'),
                    selectedColor: AppConstants.primary,
                    backgroundColor: AppConstants.darkCard,
                    labelStyle: const TextStyle(color: Colors.white, fontSize: 13),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: ChoiceChip(
                    label: const Center(child: Text('Tahunan (1 Tahun)')),
                    selected: _durasi == 'tahunan',
                    onSelected: (_) => setState(() => _durasi = 'tahunan'),
                    selectedColor: AppConstants.primary,
                    backgroundColor: AppConstants.darkCard,
                    labelStyle: const TextStyle(color: Colors.white, fontSize: 13),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 20),

            // Tanggal Mulai
            const Text('Tanggal Mulai Masuk Kos', style: TextStyle(color: Colors.white, fontSize: 14, fontWeight: FontWeight.bold)),
            const SizedBox(height: 8),
            GestureDetector(
              onTap: () async {
                final date = await showDatePicker(
                  context: context,
                  initialDate: _tanggalMulai,
                  firstDate: DateTime.now(),
                  lastDate: DateTime.now().add(const Duration(days: 90)),
                );
                if (date != null) setState(() => _tanggalMulai = date);
              },
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                decoration: BoxDecoration(
                  color: AppConstants.darkCard,
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(color: Colors.white12),
                ),
                child: Row(
                  children: [
                    const Icon(Icons.calendar_today_rounded, color: AppConstants.primaryLight, size: 18),
                    const SizedBox(width: 12),
                    Text(
                      DateFormat('dd MMMM yyyy', 'id_ID').format(_tanggalMulai),
                      style: const TextStyle(color: Colors.white, fontSize: 14),
                    ),
                    const Spacer(),
                    const Text('Ubah', style: TextStyle(color: AppConstants.primaryLight, fontSize: 12, fontWeight: FontWeight.bold)),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 20),

            // Catatan Tambahan
            const Text('Catatan Tambahan (Opsional)', style: TextStyle(color: Colors.white, fontSize: 14, fontWeight: FontWeight.bold)),
            const SizedBox(height: 8),
            TextField(
              controller: _catatanController,
              maxLines: 2,
              style: const TextStyle(color: Colors.white, fontSize: 13),
              decoration: InputDecoration(
                hintText: 'Misal: Jam perkiraan sampai di kos...',
                hintStyle: const TextStyle(color: Colors.white30),
                filled: true,
                fillColor: AppConstants.darkCard,
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(14), borderSide: BorderSide.none),
              ),
            ),
            const SizedBox(height: 28),

            // Ringkasan Pembayaran
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: AppConstants.primary.withOpacity(0.08),
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: AppConstants.primary.withOpacity(0.3)),
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.between,
                children: [
                  const Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text('Total Bayar', style: TextStyle(color: Colors.white70, fontSize: 12)),
                      Text('Termasuk Pajak & Biaya Layanan', style: TextStyle(color: Colors.white38, fontSize: 10)),
                    ],
                  ),
                  Text(
                    currencyFormatter.format(_totalBayar),
                    style: const TextStyle(color: AppConstants.primaryLight, fontSize: 18, fontWeight: FontWeight.bold),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // Checkout Button
            SizedBox(
              width: double.infinity,
              height: 52,
              child: ElevatedButton.icon(
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppConstants.primary,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                ),
                icon: const Icon(Icons.lock_rounded, color: Colors.white, size: 18),
                label: _isLoading
                    ? const CircularProgressIndicator(color: Colors.white)
                    : const Text('Bayar Sekarang via Midtrans', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 15)),
                onPressed: _isLoading ? null : _processPayment,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
