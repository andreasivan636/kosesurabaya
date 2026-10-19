import 'package:flutter/material.dart';
import '../config/constants.dart';
import '../services/ai_chat_service.dart';

class AIChatScreen extends StatefulWidget {
  final int? kosId;
  final String? kosName;

  const AIChatScreen({super.key, this.kosId, this.kosName});

  @override
  State<AIChatScreen> createState() => _AIChatScreenState();
}

class _AIChatScreenState extends State<AIChatScreen> {
  final TextEditingController _controller = TextEditingController();
  final ScrollController _scrollController = ScrollController();
  final AIChatService _chatService = AIChatService();
  bool _isTyping = false;

  final List<String> _quickPrompts = [
    '🎓 Kos murah dekat ITS',
    '🔒 Kos putri aman dekat Unair',
    '💰 Kos WiFi + AC < 1 juta',
    '🛡️ Tips anti-penipuan kos',
  ];

  @override
  void initState() {
    super.initState();
    if (widget.kosId != null && _chatService.history.isEmpty) {
      _sendMessage('Ceritakan tentang kos ${widget.kosName ?? ""} secara detail');
    }
  }

  void _scrollToBottom() {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (_scrollController.hasClients) {
        _scrollController.animateTo(
          _scrollController.position.maxScrollExtent,
          duration: const Duration(milliseconds: 300),
          curve: Curves.easeOut,
        );
      }
    });
  }

  Future<void> _sendMessage(String text) async {
    final query = text.trim();
    if (query.isEmpty || _isTyping) return;

    _controller.clear();
    setState(() => _isTyping = true);
    _scrollToBottom();

    await _chatService.sendMessage(message: query, kosId: widget.kosId);

    setState(() => _isTyping = false);
    _scrollToBottom();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppConstants.darkBg,
      appBar: AppBar(
        backgroundColor: AppConstants.darkBg,
        elevation: 0,
        title: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(6),
              decoration: BoxDecoration(
                color: AppConstants.primary.withOpacity(0.2),
                shape: BoxShape.circle,
              ),
              child: const Text('🤖', style: TextStyle(fontSize: 18)),
            ),
            const SizedBox(width: 10),
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'CS AI KoSeSurabaya',
                  style: TextStyle(color: Colors.white, fontSize: 15, fontWeight: FontWeight.bold),
                ),
                Text(
                  widget.kosName != null ? 'Konteks: ${widget.kosName}' : 'AI LLaMA 3 • Online 24 Jam',
                  style: const TextStyle(color: AppConstants.primaryLight, fontSize: 11),
                ),
              ],
            ),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.delete_outline_rounded, color: Colors.white54),
            tooltip: 'Hapus Percakapan',
            onPressed: () {
              _chatService.clearHistory();
              setState(() {});
            },
          ),
        ],
      ),
      body: Column(
        children: [
          // ─── Preset Quick Prompts ─────────────────────────
          if (_chatService.history.isEmpty)
            Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text('Tanya Cepat:', style: TextStyle(color: Colors.white54, fontSize: 12)),
                  const SizedBox(height: 8),
                  Wrap(
                    spacing: 8,
                    runSpacing: 8,
                    children: _quickPrompts.map((p) => ActionChip(
                      label: Text(p, style: const TextStyle(color: Colors.white, fontSize: 12)),
                      backgroundColor: AppConstants.darkCard,
                      side: const BorderSide(color: Colors.white12),
                      onPressed: () => _sendMessage(p.replaceFirst(RegExp(r'^[^\s]+\s'), '')),
                    )).toList(),
                  ),
                ],
              ),
            ),

          // ─── Message List ────────────────────────────────
          Expanded(
            child: ListView.builder(
              controller: _scrollController,
              padding: const EdgeInsets.all(16),
              itemCount: _chatService.history.length,
              itemBuilder: (context, index) {
                final msg = _chatService.history[index];
                return Align(
                  alignment: msg.isUser ? Alignment.centerRight : Alignment.centerLeft,
                  child: Container(
                    margin: const EdgeInsets.only(bottom: 12),
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                    constraints: BoxConstraints(maxWidth: MediaQuery.of(context).size.width * 0.8),
                    decoration: BoxDecoration(
                      color: msg.isUser ? AppConstants.primary.withOpacity(0.25) : AppConstants.darkCard,
                      borderRadius: BorderRadius.only(
                        topLeft: const Radius.circular(16),
                        topRight: const Radius.circular(16),
                        bottomLeft: Radius.circular(msg.isUser ? 16 : 4),
                        bottomRight: Radius.circular(msg.isUser ? 4 : 16),
                      ),
                      border: Border.all(
                        color: msg.isUser ? AppConstants.primary.withOpacity(0.4) : Colors.white12,
                      ),
                    ),
                    child: Text(
                      msg.content,
                      style: const TextStyle(color: Colors.white, fontSize: 13, height: 1.4),
                    ),
                  ),
                );
              },
            ),
          ),

          // ─── Typing Indicator ─────────────────────────────
          if (_isTyping)
            const Padding(
              padding: EdgeInsets.symmetric(horizontal: 20, vertical: 8),
              child: Row(
                children: [
                  SizedBox(
                    width: 16,
                    height: 16,
                    child: CircularProgressIndicator(strokeWidth: 2, color: AppConstants.primaryLight),
                  ),
                  SizedBox(width: 8),
                  Text('CS AI sedang mengetik...', style: TextStyle(color: Colors.white54, fontSize: 12)),
                ],
              ),
            ),

          // ─── Input Bar ───────────────────────────────────
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            decoration: BoxDecoration(
              color: AppConstants.darkBg,
              border: Border(top: BorderSide(color: Colors.white.withOpacity(0.08))),
            ),
            child: SafeArea(
              child: Row(
                children: [
                  Expanded(
                    child: TextField(
                      controller: _controller,
                      style: const TextStyle(color: Colors.white, fontSize: 14),
                      decoration: InputDecoration(
                        hintText: 'Tanyakan tentang kos di Surabaya...',
                        hintStyle: const TextStyle(color: Colors.white38, fontSize: 13),
                        filled: true,
                        fillColor: AppConstants.darkCard,
                        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(14),
                          borderSide: BorderSide.none,
                        ),
                      ),
                      onSubmitted: _sendMessage,
                    ),
                  ),
                  const SizedBox(width: 8),
                  IconButton(
                    style: IconButton.styleFrom(
                      backgroundColor: AppConstants.primary,
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                    ),
                    icon: const Icon(Icons.send_rounded, color: Colors.white, size: 20),
                    onPressed: () => _sendMessage(_controller.text),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}
