import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/date_symbol_data_local.dart';
import 'config/constants.dart';
import 'providers/auth_provider.dart';
import 'providers/kos_provider.dart';
import 'screens/home_map_screen.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await initializeDateFormatting('id_ID', null);

  runApp(const KoSeSurabayaApp());
}

class KoSeSurabayaApp extends StatelessWidget {
  const KoSeSurabayaApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AuthProvider()),
        ChangeNotifierProvider(create: (_) => KosProvider()),
      ],
      child: MaterialApp(
        title: 'KoSeSurabaya',
        debugShowCheckedModeBanner: false,
        theme: ThemeData(
          useMaterial3: true,
          brightness: Brightness.dark,
          scaffoldBackgroundColor: AppConstants.darkBg,
          primaryColor: AppConstants.primary,
          colorScheme: const ColorScheme.dark(
            primary: AppConstants.primary,
            secondary: AppConstants.accent,
            surface: AppConstants.darkCard,
            background: AppConstants.darkBg,
          ),
          fontFamily: 'Inter',
          appBarTheme: const AppBarTheme(
            backgroundColor: AppConstants.darkBg,
            elevation: 0,
            iconTheme: IconThemeData(color: Colors.white),
            titleTextStyle: TextStyle(
              color: Colors.white,
              fontSize: 18,
              fontWeight: FontWeight.bold,
            ),
          ),
        ),
        home: const HomeMapScreen(),
      ),
    );
  }
}
