class TransaksiModel {
  final int id;
  final String orderId;
  final int userId;
  final int kosId;
  final int tipeKamarId;
  final int jumlahBayar;
  final String durasi;
  final String tanggalMulai;
  final String tanggalSelesai;
  final String? snapToken;
  final String status;
  final String? paymentType;
  final String? namaKos;
  final String? namaTipe;

  TransaksiModel({
    required this.id,
    required this.orderId,
    required this.userId,
    required this.kosId,
    required this.tipeKamarId,
    required this.jumlahBayar,
    required this.durasi,
    required this.tanggalMulai,
    required this.tanggalSelesai,
    this.snapToken,
    required this.status,
    this.paymentType,
    this.namaKos,
    this.namaTipe,
  });

  factory TransaksiModel.fromJson(Map<String, dynamic> json) {
    return TransaksiModel(
      id: json['id'] is int ? json['id'] : int.parse((json['id'] ?? 0).toString()),
      orderId: json['order_id'] ?? '',
      userId: json['user_id'] is int ? json['user_id'] : int.parse((json['user_id'] ?? 0).toString()),
      kosId: json['kos_id'] is int ? json['kos_id'] : int.parse((json['kos_id'] ?? 0).toString()),
      tipeKamarId: json['tipe_kamar_id'] is int ? json['tipe_kamar_id'] : int.parse((json['tipe_kamar_id'] ?? 0).toString()),
      jumlahBayar: json['jumlah_bayar'] is int ? json['jumlah_bayar'] : int.parse((json['jumlah_bayar'] ?? 0).toString()),
      durasi: json['durasi'] ?? 'bulanan',
      tanggalMulai: json['tanggal_mulai'] ?? '',
      tanggalSelesai: json['tanggal_selesai'] ?? '',
      snapToken: json['snap_token'],
      status: json['status'] ?? 'pending',
      paymentType: json['payment_type'],
      namaKos: json['kos'] != null ? json['kos']['nama_kos'] : null,
      namaTipe: json['tipe_kamar'] != null ? json['tipe_kamar']['nama_tipe'] : null,
    );
  }

  bool get isPaid => status == 'paid';
  bool get isWaitingPayment => status == 'waiting_payment' || status == 'pending';
}
