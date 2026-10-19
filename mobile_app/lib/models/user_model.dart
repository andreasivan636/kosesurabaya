class UserModel {
  final int id;
  final String name;
  final String email;
  final String role; // 'customer', 'owner', 'admin'
  final String? phone;
  final String? avatar;
  final bool isActive;

  UserModel({
    required this.id,
    required this.name,
    required this.email,
    required this.role,
    this.phone,
    this.avatar,
    this.isActive = true,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: json['id'] is int ? json['id'] : int.parse(json['id'].toString()),
      name: json['name'] ?? '',
      email: json['email'] ?? '',
      role: json['role'] ?? 'customer',
      phone: json['phone'],
      avatar: json['avatar'],
      isActive: json['is_active'] == 1 || json['is_active'] == true,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'role': role,
      'phone': phone,
      'avatar': avatar,
      'is_active': isActive,
    };
  }

  bool get isCustomer => role == 'customer';
  bool get isOwner => role == 'owner';
  bool get isAdmin => role == 'admin';
}
