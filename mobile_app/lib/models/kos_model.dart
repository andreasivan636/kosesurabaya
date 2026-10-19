import 'package:latlong2/latlong.dart';
import 'tipe_kamar_model.dart';

class KosModel {
  final int id;
  final String namaKos;
  final String alamat;
  final String? kecamatan;
  final String? kelurahan;
  final String tipeKos; // 'putra', 'putri', 'campur'
  final double latitude;
  final double longitude;
  final String? deskripsi;
  final String? fotoUtama;
  final List<String> foto;
  final List<String> fasilitasUmum;
  final String? peraturan;
  final String? kontakPemilik;
  final String status;
  final double ratingAvg;
  final int ratingCount;
  final String? kampusTerdekat;
  final double? jarakKampusKm;
  final int? hargaMulai;
  final int? kamarKosong;
  final bool glow; // Dari subscription aktif (icon menyala di maps)
  final bool isVerified;
  final List<TipeKamarModel> tipeKamar;

  KosModel({
    required this.id,
    required this.namaKos,
    required this.alamat,
    this.kecamatan,
    this.kelurahan,
    required this.tipeKos,
    required this.latitude,
    required this.longitude,
    this.deskripsi,
    this.fotoUtama,
    this.foto = const [],
    this.fasilitasUmum = const [],
    this.peraturan,
    this.kontakPemilik,
    this.status = 'approved',
    this.ratingAvg = 0.0,
    this.ratingCount = 0,
    this.kampusTerdekat,
    this.jarakKampusKm,
    this.hargaMulai,
    this.kamarKosong,
    this.glow = false,
    this.isVerified = true,
    this.tipeKamar = const [],
  });

  LatLng get coordinates => LatLng(latitude, longitude);

  factory KosModel.fromJson(Map<String, dynamic> json) {
    List<String> fotoList = [];
    if (json['foto'] != null && json['foto'] is List) {
      fotoList = List<String>.from(json['foto'].map((x) => x.toString()));
    }

    List<String> fasList = [];
    if (json['fasilitas_umum'] != null && json['fasilitas_umum'] is List) {
      fasList = List<String>.from(json['fasilitas_umum'].map((x) => x.toString()));
    }

    List<TipeKamarModel> tipeList = [];
    if (json['tipe_kamar'] != null && json['tipe_kamar'] is List) {
      tipeList = (json['tipe_kamar'] as List)
          .map((x) => TipeKamarModel.fromJson(x as Map<String, dynamic>))
          .toList();
    }

    return KosModel(
      id: json['id'] is int ? json['id'] : int.parse((json['id'] ?? 0).toString()),
      namaKos: json['nama_kos'] ?? json['nama'] ?? '',
      alamat: json['alamat'] ?? '',
      kecamatan: json['kecamatan'],
      kelurahan: json['kelurahan'],
      tipeKos: json['tipe_kos'] ?? json['tipe'] ?? 'campur',
      latitude: json['latitude'] != null
          ? double.parse(json['latitude'].toString())
          : (json['lat'] != null ? double.parse(json['lat'].toString()) : -7.2756),
      longitude: json['longitude'] != null
          ? double.parse(json['longitude'].toString())
          : (json['lng'] != null ? double.parse(json['lng'].toString()) : 112.7522),
      deskripsi: json['deskripsi'],
      fotoUtama: json['foto_utama'],
      foto: fotoList,
      fasilitasUmum: fasList,
      peraturan: json['peraturan'],
      kontakPemilik: json['kontak_pemilik'],
      status: json['status'] ?? 'approved',
      ratingAvg: json['rating_avg'] != null
          ? double.parse(json['rating_avg'].toString())
          : (json['rating'] != null ? double.parse(json['rating'].toString()) : 0.0),
      ratingCount: json['rating_count'] is int ? json['rating_count'] : int.tryParse((json['rating_count'] ?? 0).toString()) ?? 0,
      kampusTerdekat: json['kampus_terdekat'],
      jarakKampusKm: json['jarak_kampus_km'] != null ? double.tryParse(json['jarak_kampus_km'].toString()) : null,
      hargaMulai: json['harga_mulai'] is int ? json['harga_mulai'] : int.tryParse((json['harga_mulai'] ?? 0).toString()),
      kamarKosong: json['kamar_kosong'] is int ? json['kamar_kosong'] : int.tryParse((json['kamar_kosong'] ?? 0).toString()),
      glow: json['glow'] == true || json['glow'] == 1,
      isVerified: (json['status'] ?? 'approved') == 'approved',
      tipeKamar: tipeList,
    );
  }
}
