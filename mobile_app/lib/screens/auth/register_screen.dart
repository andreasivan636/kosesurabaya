import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../config/constants.dart';
import '../../providers/auth_provider.dart';
import 'login_screen.dart';

class RegisterScreen extends StatefulWidget {
  const RegisterScreen({super.key});

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final _nameController = TextEditingController();
  final _emailController = TextEditingController();
  final _phoneController = TextEditingController();
  final _passwordController = TextEditingController();
  final _confirmPasswordController = TextEditingController();
  String _selectedRole = 'customer'; // 'customer' or 'owner'

  Future<void> _submit() async {
    final name = _nameController.text.trim();
    final email = _emailController.text.trim();
    final phone = _phoneController.text.trim();
    final password = _passwordController.text;
    final confirm = _confirmPasswordController.text;

    if (name.isEmpty || email.isEmpty || password.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Semua field wajib diisi')),
      );
      return;
    }

    if (password != confirm) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Konfirmasi password tidak cocok')),
      );
      return;
    }

    final auth = Provider.of<AuthProvider>(context, listen: false);
    final success = await auth.register(
      name: name,
      email: email,
      phone: phone,
      password: password,
      passwordConfirmation: confirm,
      role: _selectedRole,
    );

    if (success && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Registrasi berhasil!')),
      );
      Navigator.pop(context);
    } else if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(auth.errorMessage ?? 'Registrasi gagal')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final auth = Provider.of<AuthProvider>(context);

    return Scaffold(
      backgroundColor: AppConstants.darkBg,
      appBar: AppBar(backgroundColor: AppConstants.darkBg, elevation: 0),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'Daftar Akun Baru',
              style: TextStyle(color: Colors.white, fontSize: 24, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 6),
            const Text(
              'Pilih peran Anda dan nikmati kemudahan platform',
              style: TextStyle(color: Colors.white54, fontSize: 13),
            ),
            const SizedBox(height: 24),

            // Role Selector
            const Text('Pilih Peran Akun', style: TextStyle(color: Colors.white70, fontSize: 13, fontWeight: FontWeight.w600)),
            const SizedBox(height: 8),
            Row(
              children: [
                Expanded(
                  child: GestureDetector(
                    onTap: () => setState(() => _selectedRole = 'customer'),
                    child: Container(
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(
                        color: _selectedRole == 'customer' ? AppConstants.primary.withOpacity(0.2) : AppConstants.darkCard,
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(color: _selectedRole == 'customer' ? AppConstants.primary : Colors.white12),
                      ),
                      child: Column(
                        children: [
                          const Text('🎓', style: TextStyle(fontSize: 22)),
                          const SizedBox(height: 4),
                          Text('Mahasiswa', style: TextStyle(color: _selectedRole == 'customer' ? AppConstants.primaryLight : Colors.white70, fontWeight: FontWeight.bold, fontSize: 13)),
                        ],
                      ),
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: GestureDetector(
                    onTap: () => setState(() => _selectedRole = 'owner'),
                    child: Container(
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(
                        color: _selectedRole == 'owner' ? AppConstants.primary.withOpacity(0.2) : AppConstants.darkCard,
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(color: _selectedRole == 'owner' ? AppConstants.primary : Colors.white12),
                      ),
                      child: Column(
                        children: [
                          const Text('🏠', style: TextStyle(fontSize: 22)),
                          const SizedBox(height: 4),
                          Text('Pemilik Kos', style: TextStyle(color: _selectedRole == 'owner' ? AppConstants.primaryLight : Colors.white70, fontWeight: FontWeight.bold, fontSize: 13)),
                        ],
                      ),
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 20),

            // Nama Lengkap
            _buildField('Nama Lengkap', _nameController, 'Budi Santoso', Icons.person_outline),
            const SizedBox(height: 14),

            // Email
            _buildField('Email', _emailController, 'budi@mahasiswa.its.ac.id', Icons.email_outlined, keyboardType: TextInputType.emailAddress),
            const SizedBox(height: 14),

            // Nomor HP / WA
            _buildField('Nomor HP', _phoneController, '081234567890', Icons.phone_outlined, keyboardType: TextInputType.phone),
            const SizedBox(height: 14),

            // Password
            _buildField('Password', _passwordController, '••••••••', Icons.lock_outline, obscure: true),
            const SizedBox(height: 14),

            // Konfirmasi Password
            _buildField('Konfirmasi Password', _confirmPasswordController, '••••••••', Icons.lock_outline, obscure: true),
            const SizedBox(height: 24),

            // Submit Button
            SizedBox(
              width: double.infinity,
              height: 50,
              child: ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppConstants.primary,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                ),
                onPressed: auth.isLoading ? null : _submit,
                child: auth.isLoading
                    ? const CircularProgressIndicator(color: Colors.white)
                    : const Text('Daftar Sekarang', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 15)),
              ),
            ),
            const SizedBox(height: 16),

            // Go to Login
            Center(
              child: TextButton(
                onPressed: () => Navigator.pushReplacement(context, MaterialPageRoute(builder: (_) => const LoginScreen())),
                child: const Text('Sudah punya akun? Masuk di sini', style: TextStyle(color: AppConstants.primaryLight, fontSize: 13)),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildField(String label, TextEditingController controller, String hint, IconData icon, {bool obscure = false, TextInputType? keyboardType}) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: const TextStyle(color: Colors.white70, fontSize: 13, fontWeight: FontWeight.w600)),
        const SizedBox(height: 6),
        TextField(
          controller: controller,
          obscureText: obscure,
          keyboardType: keyboardType,
          style: const TextStyle(color: Colors.white),
          decoration: InputDecoration(
            hintText: hint,
            hintStyle: const TextStyle(color: Colors.white30),
            filled: true,
            fillColor: AppConstants.darkCard,
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(14), borderSide: BorderSide.none),
            prefixIcon: Icon(icon, color: Colors.white54),
          ),
        ),
      ],
    );
  }
}
