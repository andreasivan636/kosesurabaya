import 'package:flutter/material.dart';
import 'package:latlong2/latlong.dart';

class AppConstants {
  // ─── Base URL API (IP Lokal Laptop) ─────────────────────
  // Ganti dengan IP laptop Anda jika berpindah jaringan WiFi
  static const String serverIp = '192.168.100.9';
  static const String serverPort = '8000';
  static const String apiBaseUrl = 'http://$serverIp:$serverPort/api/v1';
  static const String storageBaseUrl = 'http://$serverIp:$serverPort/storage';

  // ─── Theme Colors ───────────────────────────────────────
  static const Color primary = Color(0xFF22C55E); // Brand Green 500
  static const Color primaryDark = Color(0xFF15803D); // Brand Green 700
  static const Color primaryLight = Color(0xFF4ADE80); // Brand Green 400
  static const Color accent = Color(0xFFFACC15); // Accent Yellow
  static const Color darkBg = Color(0xFF0F172A); // Dark 900
  static const Color darkCard = Color(0xFF1E293B); // Dark 800
  static const Color darkBorder = Color(0x1AFFFFFF); // White 10%

  // ─── Kampus Surabaya Coordinates ────────────────────────
  static final Map<String, KampusInfo> kampusList = {
    'UNTAG': KampusInfo(
      code: 'UNTAG',
      name: 'UNTAG Surabaya',
      location: LatLng(-7.3148, 112.7681),
      address: 'Jl. Semolowaru No.45, Menur Pumpungan',
    ),
    'ITS': KampusInfo(
      code: 'ITS',
      name: 'ITS Sukolilo',
      location: LatLng(-7.2820, 112.7952),
      address: 'Kampus ITS Sukolilo, Surabaya',
    ),
    'UNAIR': KampusInfo(
      code: 'UNAIR',
      name: 'Universitas Airlangga',
      location: LatLng(-7.2696, 112.7583),
      address: 'Kampus B & C UNAIR, Surabaya',
    ),
    'UNESA': KampusInfo(
      code: 'UNESA',
      name: 'UNESA Lidah Wetan',
      location: LatLng(-7.2871, 112.7189),
      address: 'Kampus Lidah Wetan / Ketintang',
    ),
  };
}

class KampusInfo {
  final String code;
  final String name;
  final LatLng location;
  final String address;

  KampusInfo({
    required this.code,
    required this.name,
    required this.location,
    required this.address,
  });
}
