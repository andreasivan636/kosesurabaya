import 'package:flutter/material.dart';
import '../models/kos_model.dart';
import '../services/api_service.dart';

class KosProvider with ChangeNotifier {
  List<KosModel> _kosList = [];
  bool _isLoading = false;
  String? _selectedKampus;
  double _radius = 2.0;
  String? _selectedTipeKos;
  int? _hargaMin;
  int? _hargaMax;
  KosModel? _selectedKos;

  List<KosModel> get kosList => _kosList;
  bool get isLoading => _isLoading;
  String? get selectedKampus => _selectedKampus;
  double get radius => _radius;
  String? get selectedTipeKos => _selectedTipeKos;
  int? get hargaMin => _hargaMin;
  int? get hargaMax => _hargaMax;
  KosModel? get selectedKos => _selectedKos;

  void setSelectedKos(KosModel? kos) {
    _selectedKos = kos;
    notifyListeners();
  }

  void setFilterKampus(String? kampus) {
    _selectedKampus = kampus;
    fetchKos();
  }

  void setRadius(double radius) {
    _radius = radius;
    fetchKos();
  }

  void setTipeKos(String? tipe) {
    _selectedTipeKos = tipe;
    fetchKos();
  }

  void setHargaRange(int? min, int? max) {
    _hargaMin = min;
    _hargaMax = max;
    fetchKos();
  }

  Future<void> fetchKos() async {
    _isLoading = true;
    notifyListeners();

    _kosList = await ApiService().getKosMarkers(
      kampus: _selectedKampus,
      radius: _radius,
      tipeKos: _selectedTipeKos,
      hargaMin: _hargaMin,
      hargaMax: _hargaMax,
    );

    _isLoading = false;
    notifyListeners();
  }
}
