import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../config/constants.dart';
import 'home_map_screen.dart';

class BookingSuccessScreen extends StatelessWidget {
  final String orderId;
  final String kosName;
  final String tipeKamarName;
  final int totalBayar;
  final DateTime tanggalMulai;
  final String durasi;

  const BookingSuccessScreen({
    super.key,
    required this.orderId,
    required this.kosName,
    required this.tipeKamarName,
    required this.totalBayar,
    required this.tanggalMulai,
    required this.durasi,
  });

  @override
  Widget build(BuildContext context) {
    final currencyFormatter = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);

    return Scaffold(
      backgroundColor: AppConstants.darkBg,
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(24),
          child: Column(
            children: [
              const SizedBox(height: 20),
              Container(
                width: 80,
                height: 80,
                decoration: BoxDecoration(
                  color: AppConstants.primary.withOpacity(0.2),
                  shape: BoxShape.circle,
                ),
                child: const Center(
                  child: Icon(Icons.check_circle_rounded, color: AppConstants.primary, size: 54),
                ),
              ),
              const SizedBox(height: 16),
              const Text(
                'Pembayaran Berhasil!',
                style: TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 6),
              const Text(
                'E-Tiket booking resmi telah terbit dan diverifikasi sistem',
                style: TextStyle(color: Colors.white54, fontSize: 13),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 28),

              // ─── E-TIKET CARD ────────────────────────────
              Container(
                decoration: BoxDecoration(
                  color: AppConstants.darkCard,
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(color: Colors.white12),
                ),
                child: Column(
                  children: [
                    // Header Tiket
                    Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: AppConstants.primary.withOpacity(0.15),
                        borderRadius: const BorderRadius.vertical(top: Radius.circular(20)),
                      ),
                      child: Row(
                        children: [
                          const Icon(Icons.confirmation_number_rounded, color: AppConstants.primaryLight, size: 20),
                          const SizedBox(width: 8),
                          const Text('E-TIKET BOOKING RESMI', style: TextStyle(color: AppConstants.primaryLight, fontWeight: FontWeight.bold, fontSize: 13)),
                          const Spacer(),
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                            decoration: BoxDecoration(
                              color: AppConstants.primary,
                              borderRadius: BorderRadius.circular(6),
                            ),
                            child: const Text('LUNAS', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 10)),
                          ),
                        ],
                      ),
                    ),

                    // Tiket Details
                    Padding(
                      padding: const EdgeInsets.all(20),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          _buildTicketRow('Nomor Order', orderId),
                          const Divider(color: Colors.white12, height: 20),
                          _buildTicketRow('Nama Kos', kosName),
                          const Divider(color: Colors.white12, height: 20),
                          _buildTicketRow('Tipe Kamar', tipeKamarName),
                          const Divider(color: Colors.white12, height: 20),
                          _buildTicketRow('Durasi Sewa', durasi == 'tahunan' ? '1 Tahun' : '1 Bulan'),
                          const Divider(color: Colors.white12, height: 20),
                          _buildTicketRow('Tanggal Masuk', DateFormat('dd MMMM yyyy', 'id_ID').format(tanggalMulai)),
                          const Divider(color: Colors.white12, height: 20),
                          _buildTicketRow('Total Dibayar', currencyFormatter.format(totalBayar), isHighlighted: true),
                        ],
                      ),
                    ),

                    // QR Code Placeholder
                    Container(
                      padding: const EdgeInsets.symmetric(vertical: 16),
                      decoration: const BoxDecoration(
                        color: Colors.black26,
                        borderRadius: BorderRadius.vertical(bottom: Radius.circular(20)),
                      ),
                      child: Column(
                        children: [
                          Container(
                            width: 100,
                            height: 100,
                            color: Colors.white,
                            padding: const EdgeInsets.all(8),
                            child: const Center(
                              child: Icon(Icons.qr_code_2_rounded, size: 80, color: Colors.black),
                            ),
                          ),
                          const SizedBox(height: 8),
                          const Text('Tunjukkan QR ini ke pemilik kos saat check-in', style: TextStyle(color: Colors.white38, fontSize: 11)),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 28),

              // Return Button
              SizedBox(
                width: double.infinity,
                height: 50,
                child: ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppConstants.primary,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                  ),
                  onPressed: () {
                    Navigator.pushAndRemoveUntil(
                      context,
                      MaterialPageRoute(builder: (_) => const HomeMapScreen()),
                      (route) => false,
                    );
                  },
                  child: const Text('Kembali ke Peta Kos', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 15)),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildTicketRow(String label, String value, {bool isHighlighted = false}) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(label, style: const TextStyle(color: Colors.white54, fontSize: 12)),
        Text(
          value,
          style: TextStyle(
            color: isHighlighted ? AppConstants.primaryLight : Colors.white,
            fontSize: isHighlighted ? 15 : 13,
            fontWeight: isHighlighted ? FontWeight.bold : FontWeight.w600,
          ),
        ),
      ],
    );
  }
}
