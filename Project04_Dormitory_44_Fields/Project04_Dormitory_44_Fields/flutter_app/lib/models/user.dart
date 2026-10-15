class User {
  final String? studentCode;
  final String? faculty;
  final String? major;

  final int id;
  final String name;
  final String email;
  final String phone;
  final String role;

  User({
    this.studentCode,
    this.faculty,
    this.major,

    required this.id,
    required this.name,
    required this.email,
    required this.phone,
    required this.role,
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      studentCode: json['student_code']?.toString(),
      faculty: json['faculty']?.toString(),
      major: json['major']?.toString(),

      id: json['id'] is int ? json['id'] : int.parse(json['id'].toString()),
      name: json['name'] ?? '',
      email: json['email'] ?? '',
      phone: json['phone'] ?? '',
      role: json['role'] ?? 'user',
    );
  }

  Map<String, dynamic> toJson() => {
    'id': id,
    'name': name,
    'email': email,
    'phone': phone,
    'role': role,
  };

  bool get isAdmin => role == 'admin';
}
