class RoomType {
  final String? description;
  final int? capacity;
  final double? depositAmount;

  final int id;
  final String name;
  final double monthlyRate;
  final String? createdAt;

  RoomType({
    this.description,
    this.capacity,
    this.depositAmount,

    required this.id,
    required this.name,
    required this.monthlyRate,
    this.createdAt,
  });

  factory RoomType.fromJson(Map<String, dynamic> json) {
    return RoomType(
      description: json['description']?.toString(),
      capacity: int.tryParse(json['capacity']?.toString() ?? ''),
      depositAmount: double.tryParse(json['deposit_amount']?.toString() ?? ''),

      id: json['id'] is int ? json['id'] : int.parse(json['id'].toString()),
      name: json['name'] ?? '',
      monthlyRate: double.tryParse(json['monthly_rate'].toString()) ?? 0.0,
      createdAt: json['created_at']?.toString(),
    );
  }

  Map<String, dynamic> toJson() => {
    'id': id,
    'name': name,
    'monthly_rate': monthlyRate,
    'created_at': createdAt,
  };
}
