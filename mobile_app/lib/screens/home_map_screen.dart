import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../config/constants.dart';
import '../models/kos_model.dart';
import '../providers/auth_provider.dart';
import '../providers/kos_provider.dart';
import 'kos_detail_screen.dart';
import 'ai_chat_screen.dart';
import 'auth/login_screen.dart';
import 'owner_dashboard_screen.dart';

class HomeMapScreen extends StatefulWidget {
  const HomeMapScreen({super.key});

  @override
  State<HomeMapScreen> createState() => _HomeMapScreenState();
}

class _HomeMapScreenState extends State<HomeMapScreen> with SingleTickerProviderStateMixin {
  final MapController _mapController = MapController();
  late AnimationController _glowAnimController;

  @override
  void initState() {
    super.initState();
    _glowAnimController = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 2),
    )..repeat(reverse: true);

    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<KosProvider>(context, listen: false).fetchKos();
    });
  }

  @override
  void dispose() {
    _glowAnimController.dispose();
    super.dispose();
  }

  void _openFilterSheet() {
    showModalBottomSheet(
      context: context,
      backgroundColor: AppConstants.darkBg,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
      builder: (ctx) {
        return Consumer<KosProvider>(
          builder: (context, provider, _) {
            return Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Center(
                    child: Container(
                      width: 40,
                      height: 4,
                      decoration: BoxDecoration(
                        color: Colors.white24,
                        borderRadius: BorderRadius.circular(2),
                      ),
                    ),
                  ),
                  const SizedBox(height: 16),
                  const Text(
                    '🔍 Filter Pencarian Kos',
                    style: TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 16),
                  const Text('📍 Kampus Terdekat', style: TextStyle(color: Colors.white70, fontSize: 13)),
                  const SizedBox(height: 8),
                  Wrap(
                    spacing: 8,
                    children: [
                      ChoiceChip(
                        label: const Text('Semua'),
                        selected: provider.selectedKampus == null,
                        onSelected: (_) => provider.setFilterKampus(null),
                        selectedColor: AppConstants.primary,
                        backgroundColor: AppConstants.darkCard,
                        labelStyle: const TextStyle(color: Colors.white),
                      ),
                      ...AppConstants.kampusList.entries.map((k) {
                        return ChoiceChip(
                          label: Text(k.key),
                          selected: provider.selectedKampus == k.key,
                          onSelected: (_) {
                            provider.setFilterKampus(k.key);
                            _mapController.move(k.value.location, 14.5);
                          },
                          selectedColor: AppConstants.primary,
                          backgroundColor: AppConstants.darkCard,
                          labelStyle: const TextStyle(color: Colors.white),
                        );
                      }),
                    ],
                  ),
                  const SizedBox(height: 16),
                  Text('📐 Radius: ${provider.radius.toStringAsFixed(1)} km', style: const TextStyle(color: Colors.white70, fontSize: 13)),
                  Slider(
                    value: provider.radius,
                    min: 0.5,
                    max: 5.0,
                    divisions: 9,
                    activeColor: AppConstants.primary,
                    inactiveColor: Colors.white12,
                    onChanged: (val) => provider.setRadius(val),
                  ),
                  const SizedBox(height: 12),
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton(
                      style: ElevatedButton.styleFrom(
                        backgroundColor: AppConstants.primary,
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                      ),
                      onPressed: () => Navigator.pop(ctx),
                      child: const Text('Terapkan Filter', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                    ),
                  ),
                ],
              ),
            );
          },
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    final kosProvider = Provider.of<KosProvider>(context);
    final authProvider = Provider.of<AuthProvider>(context);
    final currencyFormatter = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);

    // Build map markers
    List<Marker> markers = [];

    // Kampus markers
    AppConstants.kampusList.forEach((code, info) {
      markers.add(
        Marker(
          point: info.location,
          width: 50,
          height: 50,
          child: Tooltip(
            message: info.name,
            child: Container(
              decoration: BoxDecoration(
                color: AppConstants.accent.withOpacity(0.9),
                shape: BoxShape.circle,
                border: Border.all(color: Colors.white, width: 2),
                boxShadow: const [BoxShadow(color: Colors.black45, blurRadius: 8)],
              ),
              child: const Center(
                child: Text('🎓', style: TextStyle(fontSize: 20)),
              ),
            ),
          ),
        ),
      );
    });

    // Kos markers
    for (var kos in kosProvider.kosList) {
      final isSelected = kosProvider.selectedKos?.id == kos.id;

      markers.add(
        Marker(
          point: kos.coordinates,
          width: 44,
          height: 44,
          child: GestureDetector(
            onTap: () {
              kosProvider.setSelectedKos(kos);
              _mapController.move(kos.coordinates, 15);
            },
            child: AnimatedBuilder(
              animation: _glowAnimController,
              builder: (context, child) {
                final glowRadius = kos.glow ? 6.0 + (_glowAnimController.value * 8.0) : 0.0;
                return Container(
                  decoration: BoxDecoration(
                    color: isSelected ? AppConstants.accent : AppConstants.primary,
                    shape: BoxShape.circle,
                    border: Border.all(color: Colors.white, width: 2),
                    boxShadow: [
                      if (kos.glow)
                        BoxShadow(
                          color: AppConstants.primaryLight.withOpacity(0.8),
                          blurRadius: glowRadius,
                          spreadRadius: glowRadius / 2,
                        ),
                      const BoxShadow(color: Colors.black38, blurRadius: 6),
                    ],
                  ),
                  child: const Center(
                    child: Icon(Icons.home_rounded, color: Colors.white, size: 22),
                  ),
                );
              },
            ),
          ),
        ),
      );
    }

    // Circles for campus radius
    List<CircleMarker> circles = [];
    if (kosProvider.selectedKampus != null &&
        AppConstants.kampusList.containsKey(kosProvider.selectedKampus)) {
      final center = AppConstants.kampusList[kosProvider.selectedKampus]!.location;
      circles.add(
        CircleMarker(
          point: center,
          color: AppConstants.primary.withOpacity(0.12),
          borderColor: AppConstants.primary,
          borderStrokeWidth: 2,
          useRadiusInMeter: true,
          radius: kosProvider.radius * 1000,
        ),
      );
    }

    return Scaffold(
      backgroundColor: AppConstants.darkBg,
      appBar: AppBar(
        backgroundColor: AppConstants.darkBg.withOpacity(0.9),
        elevation: 0,
        title: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(6),
              decoration: BoxDecoration(
                color: AppConstants.primary,
                borderRadius: BorderRadius.circular(10),
              ),
              child: const Icon(Icons.location_city_rounded, color: Colors.white, size: 20),
            ),
            const SizedBox(width: 8),
            const Text(
              'KoSeSurabaya',
              style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 18),
            ),
          ],
        ),
        actions: [
          if (authProvider.isAuthenticated) ...[
            if (authProvider.user!.isOwner)
              IconButton(
                icon: const Icon(Icons.dashboard_rounded, color: AppConstants.primaryLight),
                tooltip: 'Dashboard Pemilik',
                onPressed: () {
                  Navigator.push(context, MaterialPageRoute(builder: (_) => const OwnerDashboardScreen()));
                },
              ),
            IconButton(
              icon: const Icon(Icons.logout_rounded, color: Colors.redAccent),
              onPressed: () => authProvider.logout(),
            ),
          ] else ...[
            TextButton(
              onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const LoginScreen())),
              child: const Text('Masuk', style: TextStyle(color: Colors.white70, fontWeight: FontWeight.w600)),
            ),
          ]
        ],
      ),
      body: Stack(
        children: [
          // ─── OSM Map ──────────────────────────────────────
          FlutterMap(
            mapController: _mapController,
            options: const MapOptions(
              initialCenter: LatLng(-7.2756, 112.7522),
              initialZoom: 13.0,
            ),
            children: [
              TileLayer(
                urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                userAgentPackageName: 'com.kosesurabaya.app',
              ),
              CircleLayer(circles: circles),
              MarkerLayer(markers: markers),
            ],
          ),

          // ─── Top Filter Trigger Button ─────────────────────
          Positioned(
            top: 16,
            left: 16,
            right: 16,
            child: GestureDetector(
              onTap: _openFilterSheet,
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                decoration: BoxDecoration(
                  color: AppConstants.darkBg.withOpacity(0.9),
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: Colors.white12),
                  boxShadow: const [BoxShadow(color: Colors.black45, blurRadius: 10)],
                ),
                child: Row(
                  children: [
                    const Icon(Icons.search_rounded, color: AppConstants.primaryLight, size: 20),
                    const SizedBox(width: 8),
                    Expanded(
                      child: Text(
                        kosProvider.selectedKampus != null
                            ? 'Dekat ${kosProvider.selectedKampus} (${kosProvider.radius} km)'
                            : 'Cari kos radius kampus Surabaya...',
                        style: const TextStyle(color: Colors.white70, fontSize: 13),
                      ),
                    ),
                    const Icon(Icons.tune_rounded, color: Colors.white54, size: 18),
                  ],
                ),
              ),
            ),
          ),

          // ─── Selected Kos Preview Card ────────────────────
          if (kosProvider.selectedKos != null)
            Positioned(
              bottom: 24,
              left: 16,
              right: 16,
              child: Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: AppConstants.darkBg.withOpacity(0.95),
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(color: Colors.white12),
                  boxShadow: const [BoxShadow(color: Colors.black54, blurRadius: 15)],
                ),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Container(
                          width: 60,
                          height: 60,
                          decoration: BoxDecoration(
                            color: AppConstants.darkCard,
                            borderRadius: BorderRadius.circular(14),
                          ),
                          child: const Center(
                            child: Icon(Icons.apartment_rounded, color: AppConstants.primary, size: 30),
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Row(
                                children: [
                                  Expanded(
                                    child: Text(
                                      kosProvider.selectedKos!.namaKos,
                                      style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 15),
                                      maxLines: 1,
                                      overflow: TextOverflow.ellipsis,
                                    ),
                                  ),
                                  if (kosProvider.selectedKos!.glow)
                                    Container(
                                      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                      decoration: BoxDecoration(
                                        color: AppConstants.primary.withOpacity(0.2),
                                        borderRadius: BorderRadius.circular(6),
                                      ),
                                      child: const Text('✨ Rekomendasi', style: TextStyle(color: AppConstants.primaryLight, fontSize: 10, fontWeight: FontWeight.bold)),
                                    ),
                                ],
                              ),
                              const SizedBox(height: 4),
                              Text(
                                kosProvider.selectedKos!.alamat,
                                style: const TextStyle(color: Colors.white54, fontSize: 11),
                                maxLines: 1,
                                overflow: TextOverflow.ellipsis,
                              ),
                              const SizedBox(height: 4),
                              Row(
                                children: [
                                  const Icon(Icons.star_rounded, color: AppConstants.accent, size: 14),
                                  const SizedBox(width: 2),
                                  Text(
                                    kosProvider.selectedKos!.ratingAvg.toStringAsFixed(1),
                                    style: const TextStyle(color: AppConstants.accent, fontSize: 12, fontWeight: FontWeight.bold),
                                  ),
                                  const SizedBox(width: 8),
                                  Text(
                                    '${kosProvider.selectedKos!.kamarKosong ?? 0} kamar kosong',
                                    style: const TextStyle(color: AppConstants.primaryLight, fontSize: 11),
                                  ),
                                ],
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 12),
                    Row(
                      children: [
                        Text(
                          kosProvider.selectedKos!.hargaMulai != null
                              ? '${currencyFormatter.format(kosProvider.selectedKos!.hargaMulai)}/bln'
                              : 'Harga N/A',
                          style: const TextStyle(color: AppConstants.primaryLight, fontWeight: FontWeight.bold, fontSize: 14),
                        ),
                        const Spacer(),
                        ElevatedButton(
                          style: ElevatedButton.styleFrom(
                            backgroundColor: AppConstants.primary,
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                          ),
                          onPressed: () {
                            Navigator.push(
                              context,
                              MaterialPageRoute(
                                builder: (_) => KosDetailScreen(kosId: kosProvider.selectedKos!.id),
                              ),
                            );
                          },
                          child: const Text('Lihat Detail →', style: TextStyle(color: Colors.white, fontSize: 12, fontWeight: FontWeight.bold)),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        backgroundColor: AppConstants.primary,
        icon: const Icon(Icons.smart_toy_rounded, color: Colors.white),
        label: const Text('Tanya CS AI', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
        onPressed: () {
          Navigator.push(context, MaterialPageRoute(builder: (_) => const AIChatScreen()));
        },
      ),
    );
  }
}
