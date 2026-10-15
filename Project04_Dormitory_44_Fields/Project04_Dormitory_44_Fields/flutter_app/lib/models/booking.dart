import 'room.dart';
import 'user.dart';

class Booking {
  final String? checkOutDate;
  final double? monthlyRate;
  final double? depositAmount;
  final String? notes;
  final String? createdAt;

  final int id;
  final String trackingCode;
  final int userId;
  final int roomId;
  final String checkInDate;
  final String status;
  final Room? room;
  final User? user;

  Booking({
    this.checkOutDate,
    this.monthlyRate,
    this.depositAmount,
    this.notes,
    this.createdAt,

    required this.id,
    required this.trackingCode,
    required this.userId,
    required this.roomId,
    required this.checkInDate,
    required this.status,
    this.room,
    this.user,
  });

  factory Booking.fromJson(Map<String, dynamic> json) {
    return Booking(
      checkOutDate: json['check_out_date']?.toString(),
      monthlyRate: double.tryParse(json['monthly_rate']?.toString() ?? ''),
      depositAmount: double.tryParse(json['deposit_amount']?.toString() ?? ''),
      notes: json['notes']?.toString(),
      createdAt: json['created_at']?.toString(),

      id: json['id'] is int ? json['id'] : int.parse(json['id'].toString()),
      trackingCode: json['tracking_code'] ?? '',
      userId: json['user_id'] is int ? json['user_id'] : int.parse(json['user_id'].toString()),
      roomId: json['room_id'] is int ? json['room_id'] : int.parse(json['room_id'].toString()),
      checkInDate: json['check_in_date'] ?? '',
      status: json['status'] ?? 'pending',
      room: json['room'] != null ? Room.fromJson(json['room']) : null,
      user: json['user'] != null ? User.fromJson(json['user']) : null,
    );
  }

  bool get isActive => status == 'active';
  bool get isPending => status == 'pending';
}
