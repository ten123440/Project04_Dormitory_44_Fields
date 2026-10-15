import 'room_type.dart';

class Room {
  final int? buildingId;

  final int id;
  final String roomNumber;
  final int roomTypeId;
  final int floor;
  final String status;
  final String? description;
  final String? createdAt;
  final RoomType? roomType;

  Room({
    this.buildingId,

    required this.id,
    required this.roomNumber,
    required this.roomTypeId,
    required this.floor,
    required this.status,
    this.description,
    this.createdAt,
    this.roomType,
  });

  factory Room.fromJson(Map<String, dynamic> json) {
    return Room(
      buildingId: int.tryParse(json['building_id']?.toString() ?? ''),

      id: json['id'] is int ? json['id'] : int.parse(json['id'].toString()),
      roomNumber: json['room_number'] ?? '',
      roomTypeId: json['room_type_id'] is int ? json['room_type_id'] : int.parse(json['room_type_id'].toString()),
      floor: json['floor'] is int ? json['floor'] : int.parse(json['floor'].toString()),
      status: json['status'] ?? 'available',
      description: json['description']?.toString(),
      createdAt: json['created_at']?.toString(),
      roomType: json['room_type'] != null ? RoomType.fromJson(json['room_type']) : null,
    );
  }

  bool get isAvailable => status == 'available';
}
