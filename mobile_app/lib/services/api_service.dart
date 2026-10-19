import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../config/constants.dart';
import '../models/kos_model.dart';
import '../models/user_model.dart';
import '../models/transaksi_model.dart';

class ApiService {
  static final ApiService _instance = ApiService._internal();
  factory ApiService() => _instance;
  ApiService._internal();

  String? _token;

  Future<void> initToken() async {
    final prefs = await SharedPreferences.getInstance();
    _token = prefs.getString('auth_token');
  }

  Future<void> setToken(String token) async {
    _token = token;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('auth_token', token);
  }

  Future<void> clearToken() async {
    _token = null;
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('auth_token');
    await prefs.remove('user_data');
  }

  Map<String, String> _headers({bool needsAuth = false}) {
    final headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };
    if (needsAuth && _token != null) {
      headers['Authorization'] = 'Bearer $_token';
    }
    return headers;
  }

  // ─── AUTH APIS ──────────────────────────────────────────
  Future<Map<String, dynamic>> login(String email, String password) async {
    try {
      final res = await http.post(
        Uri.parse('${AppConstants.apiBaseUrl}/login'),
        headers: _headers(),
        body: jsonEncode({'email': email, 'password': password}),
      );

      final data = jsonDecode(res.body);
      if (res.statusCode == 200 && data['success'] == true) {
        final token = data['token'];
        await setToken(token);
        final user = UserModel.fromJson(data['user']);
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('user_data', jsonEncode(data['user']));
        return {'success': true, 'user': user, 'token': token};
      }
      return {'success': false, 'message': data['message'] ?? 'Login gagal'};
    } catch (e) {
      return {'success': false, 'message': 'Gagal terhubung ke server ($e)'};
    }
  }

  Future<Map<String, dynamic>> register({
    required String name,
    required String email,
    required String password,
    required String passwordConfirmation,
    required String role,
    String? phone,
  }) async {
    try {
      final res = await http.post(
        Uri.parse('${AppConstants.apiBaseUrl}/register'),
        headers: _headers(),
        body: jsonEncode({
          'name': name,
          'email': email,
          'password': password,
          'password_confirmation': passwordConfirmation,
          'role': role,
          'phone': phone,
        }),
      );

      final data = jsonDecode(res.body);
      if (res.statusCode == 201 && data['success'] == true) {
        final token = data['token'];
        await setToken(token);
        final user = UserModel.fromJson(data['user']);
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('user_data', jsonEncode(data['user']));
        return {'success': true, 'user': user, 'token': token};
      }
      return {'success': false, 'message': data['message'] ?? 'Registrasi gagal'};
    } catch (e) {
      return {'success': false, 'message': 'Gagal terhubung ke server ($e)'};
    }
  }

  Future<void> logout() async {
    try {
      if (_token != null) {
        await http.post(
          Uri.parse('${AppConstants.apiBaseUrl}/logout'),
          headers: _headers(needsAuth: true),
        );
      }
    } catch (_) {}
    await clearToken();
  }

  // ─── KOS & MAP APIS (Guest Accessible) ───────────────────
  Future<List<KosModel>> getKosMarkers({
    String? kampus,
    double radius = 2.0,
    String? tipeKos,
    int? hargaMin,
    int? hargaMax,
  }) async {
    try {
      final queryParams = <String, String>{
        'radius': radius.toString(),
      };
      if (kampus != null && kampus.isNotEmpty) queryParams['kampus'] = kampus;
      if (tipeKos != null && tipeKos.isNotEmpty) queryParams['tipe_kos'] = tipeKos;
      if (hargaMin != null) queryParams['harga_min'] = hargaMin.toString();
      if (hargaMax != null) queryParams['harga_max'] = hargaMax.toString();

      final uri = Uri.parse('${AppConstants.apiBaseUrl}/kos/markers').replace(queryParameters: queryParams);
      final res = await http.get(uri, headers: _headers());

      if (res.statusCode == 200) {
        final data = jsonDecode(res.body);
        if (data['data'] is List) {
          return (data['data'] as List).map((k) => KosModel.fromJson(k)).toList();
        }
      }
      return [];
    } catch (e) {
      print('Error getKosMarkers: $e');
      return [];
    }
  }

  Future<KosModel?> getKosDetail(int id) async {
    try {
      final res = await http.get(
        Uri.parse('${AppConstants.apiBaseUrl}/kos/$id'),
        headers: _headers(),
      );

      if (res.statusCode == 200) {
        final data = jsonDecode(res.body);
        // API could return blade or json depending on headers
        if (data is Map<String, dynamic> && data['id'] != null) {
          return KosModel.fromJson(data);
        }
      }
      return null;
    } catch (e) {
      print('Error getKosDetail: $e');
      return null;
    }
  }

  // ─── BOOKING & PAYMENT APIS (Auth Required) ──────────────
  Future<Map<String, dynamic>> createBooking({
    required int tipeKamarId,
    required String durasi,
    required String tanggalMulai,
    String? catatan,
  }) async {
    try {
      final res = await http.post(
        Uri.parse('${AppConstants.apiBaseUrl}/booking'),
        headers: _headers(needsAuth: true),
        body: jsonEncode({
          'tipe_kamar_id': tipeKamarId,
          'durasi': durasi,
          'tanggal_mulai': tanggalMulai,
          'catatan': catatan,
        }),
      );

      final data = jsonDecode(res.body);
      if (res.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'snap_token': data['snap_token'],
          'order_id': data['order_id'],
        };
      }
      return {
        'success': false,
        'message': data['message'] ?? 'Gagal membuat booking',
      };
    } catch (e) {
      return {'success': false, 'message': 'Kesalahan jaringan: $e'};
    }
  }

  Future<List<TransaksiModel>> getTransaksiHistory() async {
    try {
      final res = await http.get(
        Uri.parse('${AppConstants.apiBaseUrl}/transaksi'),
        headers: _headers(needsAuth: true),
      );

      if (res.statusCode == 200) {
        final data = jsonDecode(res.body);
        final list = data['data'] is List ? data['data'] as List : [];
        return list.map((x) => TransaksiModel.fromJson(x)).toList();
      }
      return [];
    } catch (e) {
      print('Error getTransaksiHistory: $e');
      return [];
    }
  }
}
