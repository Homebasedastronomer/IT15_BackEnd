import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

type Overview = {
    students: number;
    courses: number;
    school_days: number;
    average_attendance: number;
};

type CourseDistribution = {
    name: string;
    code: string;
    enrolled_count: number;
};

type LatestStudent = {
    id: number;
    student_number: string;
    first_name: string;
    last_name: string;
    year_level: number;
    course: {
        id: number;
        code: string;
        name: string;
    } | null;
};

type Props = {
    overview: Overview;
    latestStudents: LatestStudent[];
    courseDistribution: CourseDistribution[];
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

export default function Dashboard({
    overview,
    latestStudents,
    courseDistribution,
}: Props) {
    const attendanceStatus =
        overview.average_attendance >= 90
            ? 'Excellent'
            : overview.average_attendance >= 80
              ? 'Good'
              : 'Needs attention';

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl bg-slate-50 p-4">
                <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div className="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                        <p className="text-xs uppercase tracking-wide text-slate-500">
                            Students
                        </p>
                        <p className="mt-2 text-3xl font-semibold text-slate-900">
                            {overview.students.toLocaleString()}
                        </p>
                    </div>
                    <div className="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                        <p className="text-xs uppercase tracking-wide text-slate-500">
                            Courses
                        </p>
                        <p className="mt-2 text-3xl font-semibold text-slate-900">
                            {overview.courses.toLocaleString()}
                        </p>
                    </div>
                    <div className="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                        <p className="text-xs uppercase tracking-wide text-slate-500">
                            School Days
                        </p>
                        <p className="mt-2 text-3xl font-semibold text-slate-900">
                            {overview.school_days.toLocaleString()}
                        </p>
                    </div>
                    <div className="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                        <p className="text-xs uppercase tracking-wide text-slate-500">
                            Avg Attendance
                        </p>
                        <p className="mt-2 text-3xl font-semibold text-slate-900">
                            {overview.average_attendance.toFixed(2)}%
                        </p>
                        <p className="mt-1 text-sm text-slate-600">
                            {attendanceStatus}
                        </p>
                    </div>
                </div>

                <div className="grid gap-4 xl:grid-cols-2">
                    <section className="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                        <h2 className="text-base font-semibold text-slate-900">
                            Latest Enrollees
                        </h2>

                        {latestStudents.length === 0 ? (
                            <p className="mt-3 text-sm text-slate-500">
                                No students enrolled yet.
                            </p>
                        ) : (
                            <div className="mt-3 overflow-x-auto">
                                <table className="min-w-full text-left text-sm text-slate-700">
                                    <thead className="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500">
                                        <tr>
                                            <th className="px-2 py-2">Student No.</th>
                                            <th className="px-2 py-2">Name</th>
                                            <th className="px-2 py-2">Program</th>
                                            <th className="px-2 py-2">Year</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {latestStudents.map((student) => (
                                            <tr
                                                key={student.id}
                                                className="border-b border-slate-100"
                                            >
                                                <td className="px-2 py-2 font-medium text-slate-900">
                                                    {student.student_number}
                                                </td>
                                                <td className="px-2 py-2">
                                                    {student.first_name}{' '}
                                                    {student.last_name}
                                                </td>
                                                <td className="px-2 py-2">
                                                    {student.course?.code ?? 'N/A'}
                                                </td>
                                                <td className="px-2 py-2">
                                                    {student.year_level}
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        )}
                    </section>

                    <section className="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                        <h2 className="text-base font-semibold text-slate-900">
                            Top Programs by Enrollment
                        </h2>

                        {courseDistribution.length === 0 ? (
                            <p className="mt-3 text-sm text-slate-500">
                                No courses available yet.
                            </p>
                        ) : (
                            <ul className="mt-4 space-y-3">
                                {courseDistribution.map((course) => (
                                    <li key={course.code}>
                                        <div className="mb-1 flex items-center justify-between text-sm">
                                            <span className="font-medium text-slate-900">{course.code}</span>
                                            <span className="text-slate-600">
                                                {course.enrolled_count} students
                                            </span>
                                        </div>
                                        <p className="mb-2 text-xs text-slate-500">
                                            {course.name}
                                        </p>
                                        <div className="h-2 rounded-full bg-slate-100">
                                            <div
                                                className="h-2 rounded-full bg-slate-900"
                                                style={{
                                                    width: `${Math.max(
                                                        8,
                                                        Math.min(100, course.enrolled_count),
                                                    )}%`,
                                                }}
                                            />
                                        </div>
                                    </li>
                                ))}
                            </ul>
                        )}
                    </section>
                </div>
            </div>
        </AppLayout>
    );
}
