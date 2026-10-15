<?php $__env->startSection('content'); ?>
<div class="max-w-md mx-auto my-8">
    <div class="bg-white p-8 rounded-2xl shadow-xl border border-slate-200">
        <div class="text-center mb-6">
            <div class="w-14 h-14 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-inner">
                <span class="material-icons text-3xl">person_add</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900">ลงทะเบียนนักศึกษาใหม่</h1>
            <p class="text-sm text-slate-500 mt-1">กรอกข้อมูลเพื่อเริ่มต้นจองห้องพักในหอพัก</p>
        </div>

        <form action="<?php echo e(route('register')); ?>" method="POST" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">ชื่อ - นามสกุล</label>
                <div class="relative">
                    <span class="material-icons absolute left-3 top-2.5 text-slate-400 text-lg">person</span>
                    <input type="text" name="name" id="name" required value="<?php echo e(old('name')); ?>"
                        class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none transition"
                        placeholder="สมชาย สุขเกษม">
                </div>
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">อีเมล</label>
                <div class="relative">
                    <span class="material-icons absolute left-3 top-2.5 text-slate-400 text-lg">email</span>
                    <input type="email" name="email" id="email" required value="<?php echo e(old('email')); ?>"
                        class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none transition"
                        placeholder="student@dormitory.com">
                </div>
            </div>

            <div>
                <label for="phone" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">เบอร์โทรศัพท์ติดต่อ</label>
                <div class="relative">
                    <span class="material-icons absolute left-3 top-2.5 text-slate-400 text-lg">phone</span>
                    <input type="text" name="phone" id="phone" required value="<?php echo e(old('phone')); ?>"
                        class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none transition"
                        placeholder="0812345678">
                </div>
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">รหัสผ่าน (อย่างน้อย 6 ตัวอักษร)</label>
                <div class="relative">
                    <span class="material-icons absolute left-3 top-2.5 text-slate-400 text-lg">lock</span>
                    <input type="password" name="password" id="password" required
                        class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none transition"
                        placeholder="••••••••">
                </div>
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">ยืนยันรหัสผ่านอีกครั้ง</label>
                <div class="relative">
                    <span class="material-icons absolute left-3 top-2.5 text-slate-400 text-lg">lock_outline</span>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none transition"
                        placeholder="••••••••">
                </div>
            </div>

            <label class="block text-xs font-semibold text-slate-700">รหัสนักศึกษา (ไม่บังคับ)<input class="w-full border rounded-xl p-2 mt-1 mb-3" type="text" name="student_code" value="<?php echo e(old('student_code')); ?>" maxlength="30"></label>
<label class="block text-xs font-semibold text-slate-700">คณะ<input class="w-full border rounded-xl p-2 mt-1 mb-3" type="text" name="faculty" value="<?php echo e(old('faculty')); ?>" maxlength="150"></label>
<label class="block text-xs font-semibold text-slate-700">สาขาวิชา<input class="w-full border rounded-xl p-2 mt-1 mb-3" type="text" name="major" value="<?php echo e(old('major')); ?>" maxlength="150"></label>
<button type="submit"
                class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl shadow-md shadow-indigo-200 transition flex items-center justify-center gap-2">
                <span class="material-icons text-sm">how_to_reg</span> ยืนยันการสมัครสมาชิก
            </button>
        </form>

        <div class="text-center mt-6 text-xs text-slate-500">
            มีบัญชีผู้ใช้งานอยู่แล้ว?
            <a href="<?php echo e(route('login')); ?>" class="text-indigo-600 font-semibold hover:underline">เข้าสู่ระบบที่นี่</a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ASUS\Downloads\Project04_Dormitory_44_Fields\Project04_Dormitory_44_Fields\backend\resources\views/auth/register.blade.php ENDPATH**/ ?>