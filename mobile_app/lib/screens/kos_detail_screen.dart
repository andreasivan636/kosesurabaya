import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../config/constants.dart';
import '../models/kos_model.dart';
import '../models/tipe_kamar_model.dart';
import '../providers/auth_provider.dart';
import '../services/api_service.dart';
import 'ai_chat_screen.dart';
import 'auth/login_screen.dart';
import 'booking_checkout_screen.dart';

class KosDetailScreen extends StatefulWidget {
  final int kosId;
  const KosDetailScreen({super.key, required this.kosId});

  @override
  State<KosDetailScreen> createState() => _KosDetailScreenState();
}

class _KosDetailScreenState extends State<KosDetailScreen> {
  KosModel? _kos;
  bool _isLoading = true;
  TipeKamarModel? _selectedTipeKamar;

  @override
  void initState() {
    super.initState();
    _loadDetail();
  }

  Future<void> _loadDetail() async {
    setState(() => _isLoading = true);
    // Fetch detail from API or mock fallback from marker
    final detail = await ApiService().getKosDetail(widget.kosId);
    if (detail != null) {
      setState(() {
        _kos = detail;
        if (detail.tipeKamar.isNotEmpty) {
          _selectedTipeKamar = detail.tipeKamar.first;
        }
        _isLoading = false;
      });
    } else {
      // Fallback demo data if single detail route is HTML
      final markers = await ApiService().getKosMarkers();
      final found = markers.where((k) => k.id == widget.kosId).firstOrNull;
      setState(() {
        _kos = found;
        _isLoading = false;
      });
    }
  }

  void _handleBooking() {
    final authProvider = Provider.of<AuthProvider>(context, listen: false);

    if (_selectedTipeKamar == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Silakan pilih salah satu tipe kamar (A, B, atau C)')),
      );
      return;
    }

    if (!authProvider.isAuthenticated) {
      // Customer belum login -> arahkan login/register sesuai flow
      showDialog(
        context: context,
        builder: (ctx) => AlertDialog(
          backgroundColor: AppConstants.darkBg,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
          title: const Row(
            children: [
              Icon(Icons.lock_rounded, color: AppConstants.accent),
              SizedBox(width: 8),
              Text('Login Diperlukan', style: TextStyle(color: Colors.white, fontSize: 16)),
            ],
          ),
          content: const Text(
            'Untuk keamanan transaksi dan penerbitan e-tiket booking resmi via Midtrans, silakan login terlebih dahulu.',
            style: TextStyle(color: Colors.white70, fontSize: 13),
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(ctx),
              child: const Text('Batal', style: TextStyle(color: Colors.white54)),
            ),
            ElevatedButton(
              style: ElevatedButton.styleFrom(backgroundColor: AppConstants.primary),
              onPressed: () {
                Navigator.pop(ctx);
                Navigator.push(context, MaterialPageRoute(builder: (_) => const LoginScreen()));
              },
              child: const Text('Login / Daftar', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
            ),
          ],
        ),
      );
      return;
    }

    // Authenticated -> buka checkout booking
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) => BookingCheckoutScreen(
          kos: _kos!,
          tipeKamar: _selectedTipeKamar!,
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final currencyFormatter = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);

    if (_isLoading) {
      return const Scaffold(
        backgroundColor: AppConstants.darkBg,
        body: Center(child: CircularProgressIndicator(color: AppConstants.primary)),
      );
    }

    if (_kos == null) {
      return Scaffold(
        backgroundColor: AppConstants.darkBg,
        appBar: AppBar(backgroundColor: AppConstants.darkBg, elevation: 0),
        body: const Center(
          child: Text('Kos tidak ditemukan', style: TextStyle(color: Colors.white)),
        ),
      );
    }

    return Scaffold(
      backgroundColor: AppConstants.darkBg,
      body: CustomScrollView(
        slivers: [
          // ─── Sliver App Bar with Image ───────────────────
          SliverAppBar(
            expandedHeight: 250,
            pinned: true,
            backgroundColor: AppConstants.darkBg,
            leading: IconButton(
              icon: Container(
                padding: const EdgeInsets.all(8),
                decoration: const BoxDecoration(
                  color: Colors.black45,
                  shape: BoxShape.circle,
                ),
                child: const Icon(Icons.arrow_back, color: Colors.white, size: 20),
              ),
              onPressed: () => Navigator.pop(context),
            ),
            flexibleSpace: FlexibleSpaceBar(
              background: Container(
                color: AppConstants.darkCard,
                child: const Center(
                  child: Icon(Icons.apartment_rounded, color: AppConstants.primary, size: 80),
                ),
              ),
            ),
          ),

          // ─── Content ─────────────────────────────────────
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Nama & Verified Badge
                  Row(
                    children: [
                      Expanded(
                        child: Text(
                          _kos!.namaKos,
                          style: const TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.bold),
                        ),
                      ),
                      if (_kos!.isVerified)
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                          decoration: BoxDecoration(
                            color: AppConstants.primary.withOpacity(0.2),
                            borderRadius: BorderRadius.circular(8),
                            border: Border.all(color: AppConstants.primary.withOpacity(0.4)),
                          ),
                          child: const Row(
                            children: [
                              Icon(Icons.verified_rounded, color: AppConstants.primaryLight, size: 14),
                              SizedBox(width: 4),
                              Text('Terverifikasi', style: TextStyle(color: AppConstants.primaryLight, fontSize: 11, fontWeight: FontWeight.bold)),
                            ],
                          ),
                        ),
                    ],
                  ),
                  const SizedBox(height: 6),
                  Row(
                    children: [
                      const Icon(Icons.location_on_rounded, color: AppConstants.primaryLight, size: 16),
                      const SizedBox(width: 4),
                      Expanded(
                        child: Text(_kos!.alamat, style: const TextStyle(color: Colors.white70, fontSize: 13)),
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),

                  // Chips: Tipe, Kampus, Rating
                  Wrap(
                    spacing: 8,
                    runSpacing: 8,
                    children: [
                      _buildChip('🏠 Kos ${capitalize(_kos!.tipeKos)}', Colors.blue),
                      if (_kos!.kampusTerdekat != null)
                        _buildChip('🎓 Dekat ${_kos!.kampusTerdekat}', Colors.amber),
                      _buildChip('⭐ ${_kos!.ratingAvg.toStringAsFixed(1)} (${_kos!.ratingCount} ulasan)', Colors.orange),
                    ],
                  ),
                  const SizedBox(height: 20),

                  // Deskripsi
                  if (_kos!.deskripsi != null) ...[
                    const Text('Deskripsi', style: TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.bold)),
                    const SizedBox(height: 8),
                    Text(
                      _kos!.deskripsi!,
                      style: const TextStyle(color: Colors.white70, fontSize: 13, height: 1.5),
                    ),
                    const SizedBox(height: 20),
                  ],

                  // ─── 3 TIER ROOM CATEGORIES (Tipe A, B, C) ──
                  const Text('Pilihan Tipe Kamar', style: TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.bold)),
                  const SizedBox(height: 12),

                  if (_kos!.tipeKamar.isNotEmpty)
                    ..._kos!.tipeKamar.map((tipe) => _buildRoomTierCard(tipe, currencyFormatter))
                  else ...[
                    // Demo fallback 3 Tiers jika belum fetch deep relations
                    _buildDemoRoomTier('A', 'Tipe A (Deluxe)', 1500000, ['AC', 'WiFi', 'Kamar Mandi Dalam', 'Kasur', 'Meja Belajar'], currencyFormatter),
                    _buildDemoRoomTier('B', 'Tipe B (Standard)', 1000000, ['Kipas Angin', 'WiFi', 'Kamar Mandi Dalam', 'Kasur'], currencyFormatter),
                    _buildDemoRoomTier('C', 'Tipe C (Ekonomi)', 600000, ['Kipas Angin', 'Kasur', 'Kamar Mandi Luar'], currencyFormatter),
                  ],

                  const SizedBox(height: 100), // Spacing for floating bottom bar
                ],
              ),
            ),
          ),
        ],
      ),

      // ─── FLOATING BOTTOM BAR ─────────────────────────────
      bottomSheet: Container(
        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 14),
        decoration: BoxDecoration(
          color: AppConstants.darkBg,
          border: Border(top: BorderSide(color: Colors.white.withOpacity(0.08))),
          boxShadow: const [BoxShadow(color: Colors.black87, blurRadius: 20)],
        ),
        child: SafeArea(
          child: Row(
            children: [
              // Price
              Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text('Mulai dari', style: TextStyle(color: Colors.white54, fontSize: 11)),
                  Text(
                    _selectedTipeKamar != null
                        ? '${currencyFormatter.format(_selectedTipeKamar!.hargaBulanan)}/bln'
                        : (_kos!.hargaMulai != null ? '${currencyFormatter.format(_kos!.hargaMulai)}/bln' : '-'),
                    style: const TextStyle(color: AppConstants.primaryLight, fontSize: 16, fontWeight: FontWeight.bold),
                  ),
                ],
              ),
              const Spacer(),

              // Tanya CS AI Button
              OutlinedButton.icon(
                style: OutlinedButton.styleFrom(
                  side: const BorderSide(color: AppConstants.accent),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                ),
                icon: const Icon(Icons.smart_toy_rounded, color: AppConstants.accent, size: 18),
                label: const Text('Tanya AI', style: TextStyle(color: AppConstants.accent, fontSize: 13, fontWeight: FontWeight.bold)),
                onPressed: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (_) => AIChatScreen(kosId: _kos!.id, kosName: _kos!.namaKos),
                    ),
                  );
                },
              ),
              const SizedBox(width: 8),

              // Booking Button
              ElevatedButton.icon(
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppConstants.primary,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 12),
                ),
                icon: const Icon(Icons.calendar_month_rounded, color: Colors.white, size: 18),
                label: const Text('Booking', style: TextStyle(color: Colors.white, fontSize: 13, fontWeight: FontWeight.bold)),
                onPressed: _handleBooking,
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildChip(String label, MaterialColor color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
      decoration: BoxDecoration(
        color: color.withOpacity(0.15),
        borderRadius: BorderRadius.circular(8),
      ),
      child: Text(label, style: TextStyle(color: color.shade300, fontSize: 12, fontWeight: FontWeight.w600)),
    );
  }

  Widget _buildRoomTierCard(TipeKamarModel tipe, NumberFormat formatter) {
    final isSelected = _selectedTipeKamar?.id == tipe.id;
    return GestureDetector(
      onTap: () => setState(() => _selectedTipeKamar = tipe),
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: AppConstants.darkCard,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(
            color: isSelected ? AppConstants.primary : Colors.white12,
            width: isSelected ? 2 : 1,
          ),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    color: tipe.tipe == 'A'
                        ? AppConstants.accent.withOpacity(0.2)
                        : (tipe.tipe == 'B' ? AppConstants.primary.withOpacity(0.2) : Colors.white10),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Text(
                    'Tipe ${tipe.tipe}',
                    style: TextStyle(
                      color: tipe.tipe == 'A'
                          ? AppConstants.accent
                          : (tipe.tipe == 'B' ? AppConstants.primaryLight : Colors.white70),
                      fontWeight: FontWeight.bold,
                      fontSize: 12,
                    ),
                  ),
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: Text(
                    tipe.namaTipe,
                    style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 15),
                  ),
                ),
                Text(
                  '${formatter.format(tipe.hargaBulanan)}/bln',
                  style: const TextStyle(color: AppConstants.primaryLight, fontWeight: FontWeight.bold, fontSize: 14),
                ),
              ],
            ),
            const SizedBox(height: 8),
            Wrap(
              spacing: 6,
              runSpacing: 4,
              children: tipe.fasilitas.map((f) => Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.05),
                  borderRadius: BorderRadius.circular(6),
                ),
                child: Text('✓ $f', style: const TextStyle(color: Colors.white70, fontSize: 11)),
              )).toList(),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildDemoRoomTier(String code, String name, int price, List<String> fas, NumberFormat formatter) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: AppConstants.darkCard,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.white12),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Text(name, style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 15)),
              const Spacer(),
              Text('${formatter.format(price)}/bln', style: const TextStyle(color: AppConstants.primaryLight, fontWeight: FontWeight.bold, fontSize: 14)),
            ],
          ),
          const SizedBox(height: 8),
          Wrap(
            spacing: 6,
            children: fas.map((f) => Text('✓ $f  ', style: const TextStyle(color: Colors.white70, fontSize: 11))).toList(),
          ),
        ],
      ),
    );
  }

  String capitalize(String s) => s.isNotEmpty ? '${s[0].toUpperCase()}${s.substring(1)}' : '';
}
