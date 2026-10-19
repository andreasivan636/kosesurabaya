import 'dart:convert';
import 'package:http/http.dart' as http;
import '../config/constants.dart';

class ChatMessage {
  final String role; // 'user' or 'assistant'
  final String content;
  final DateTime timestamp;

  ChatMessage({
    required this.role,
    required this.content,
    DateTime? timestamp,
  }) : timestamp = timestamp ?? DateTime.now();

  bool get isUser => role == 'user';

  Map<String, dynamic> toJson() => {
    'role': role,
    'content': content,
  };
}

class AIChatService {
  static final AIChatService _instance = AIChatService._internal();
  factory AIChatService() => _instance;
  AIChatService._internal();

  final List<ChatMessage> _history = [];
  List<ChatMessage> get history => List.unmodifiable(_history);

  void clearHistory() {
    _history.clear();
  }

  Future<String> sendMessage({
    required String message,
    int? kosId,
  }) async {
    _history.add(ChatMessage(role: 'user', content: message));

    try {
      final res = await http.post(
        Uri.parse('${AppConstants.apiBaseUrl}/ai/chat'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode({
          'message': message,
          'kos_id': kosId,
          'conversation_history': _history.sublist(0, _history.length - 1).map((m) => m.toJson()).toList(),
        }),
      );

      if (res.statusCode == 200) {
        final data = jsonDecode(res.body);
        final reply = data['response'] ?? 'Maaf, tidak ada respon dari AI.';
        _history.add(ChatMessage(role: 'assistant', content: reply));
        return reply;
      } else {
        const errorMsg = '⚠️ CS AI sedang offline. Pastikan Ollama berjalan di localhost:11434.';
        _history.add(ChatMessage(role: 'assistant', content: errorMsg));
        return errorMsg;
      }
    } catch (e) {
      final errorMsg = '⚠️ Gagal terhubung ke CS AI ($e). Pastikan Ollama aktif.';
      _history.add(ChatMessage(role: 'assistant', content: errorMsg));
      return errorMsg;
    }
  }
}
