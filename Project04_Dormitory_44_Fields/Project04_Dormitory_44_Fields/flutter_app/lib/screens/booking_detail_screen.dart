import 'package:flutter/material.dart';
import '../models/booking.dart';
import '../services/api_service.dart';

class BookingDetailScreen extends StatefulWidget {
  final String trackingCode;

  const BookingDetailScreen({super.key, required this.trackingCode});

  @override
  State<BookingDetailScreen> createState() => _BookingDetailScreenState();
}

class _BookingDetailScreenState extends State<BookingDetailScreen> {
  Booking? _booking;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadDetails();
  }

  Future<void> _loadDetails() async {
    setState(() => _isLoading = true);
    final b = await ApiService.getBookingByCode(widget.trackingCode);
    setState(() {
      _booking = b;
      _isLoading = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text("สัญญา " + widget.trackingCode),
        backgroundColor: Colors.indigo,
        foregroundColor: Colors.white,
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _booking == null
              ? const Center(child: Text('ไม่พบข้อมูลสัญญาเช่า'))
              : Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: ListView(
                    children: [
                      Card(
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                        elevation: 2,
                        child: Padding(
                          padding: const EdgeInsets.all(20.0),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Row(
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                children: [
                                  Text(
                                    _booking!.trackingCode,
                                    style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: Colors.indigo),
                                  ),
                                  Chip(
                                    label: Text(_booking!.status),
                                    backgroundColor: _booking!.isActive ? Colors.green.shade100 : Colors.amber.shade100,
                                    labelStyle: TextStyle(
                                      color: _booking!.isActive ? Colors.green.shade900 : Colors.amber.shade900,
                                      fontWeight: FontWeight.bold,
                                      fontSize: 12,
                                    ),
                                  ),
                                ],
                              ),
                              const Divider(height: 24),
                              const Text('ข้อมูลห้องพัก', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                              const SizedBox(height: 8),
                              Text("หมายเลขห้อง: ห้อง " + (_booking!.room?.roomNumber ?? '-')),
                              Text("ประเภทห้อง: " + (_booking!.room?.roomType?.name ?? '-')),
                              Text("ชั้น: ชั้น " + (_booking!.room?.floor.toString() ?? '-')),
                              Text("อัตราค่าเช่า: ฿" + (_booking!.room?.roomType?.monthlyRate.toStringAsFixed(2) ?? '0.00') + " / เดือน"),
                              const SizedBox(height: 16),
                              const Text('ข้อมูลผู้เช่า', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                              const SizedBox(height: 8),
                              Text("ชื่อผู้เช่า: " + (_booking!.user?.name ?? '-')),
                              Text("เบอร์โทร: " + (_booking!.user?.phone ?? '-')),
                              Text("อีเมล: " + (_booking!.user?.email ?? '-')),
                              Text("วันที่เริ่มเข้าพัก: " + _booking!.checkInDate),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
    );
  }
}
