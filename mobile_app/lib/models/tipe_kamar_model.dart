class TipeKamarModel {
  final int id;
  final int kosId;
  final String tipe; // 'A', 'B', 'C'
  final String namaTipe;
  final String? deskripsi;
  final int hargaBulanan;
  final int? hargaTahunan;
  final int jumlahKamar;
  final int kamarTersedia;
  final double? ukuranKamar;
  final List<String> fasilitas;

  TipeKamarModel({
    required this.id,
    required this.kosId,
    required this.tipe,
    required this.namaTipe,
    this.deskripsi,
    required this.hargaBulanan,
    this.hargaTahunan,
    required this.jumlahKamar,
    required this.kamarTersedia,
    this.ukuranKamar,
    required this.fasilitas,
  });

  factory TipeKamarModel.fromJson(Map<String, dynamic> json) {
    List<String> fasList = [];
    if (json['fasilitas'] != null) {
      if (json['fasilitas'] is List) {
        fasList = List<String>.from(json['fasilitas'].map((x) => x.toString()));
      }
    }

    return TipeKamarModel(
      id: json['id'] is int ? json['id'] : int.parse(json['id'].toString()),
      kosId: json['kos_id'] is int ? json['kos_id'] : int.parse((json['kos_id'] ?? 0).toString()),
      tipe: json['tipe'] ?? 'A',
      namaTipe: json['nama_tipe'] ?? '',
      deskripsi: json['deskripsi'],
      hargaBulanan: json['harga_bulanan'] is int ? json['harga_bulanan'] : int.parse((json['harga_bulanan'] ?? 0).toString()),
      hargaTahunan: json['harga_tahunan'] != null ? int.tryParse(json['harga_tahunan'].toString()) : null,
      jumlahKamar: json['jumlah_kamar'] is int ? json['jumlah_kamar'] : int.parse((json['jumlah_kamar'] ?? 1).toString()),
      kamarTersedia: json['kamar_tersedia'] is int ? json['kamar_tersedia'] : int.parse((json['kamar_tersedia'] ?? 0).toString()),
      ukuranKamar: json['ukuran_kamar'] != null ? double.tryParse(json['ukuran_kamar'].toString()) : null,
      fasilitas: fasList,
    );
  }

  bool get isAvailable => kamarTersedia > 0;
}
