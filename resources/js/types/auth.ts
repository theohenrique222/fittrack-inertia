export type User = {
    id: number;
    name: string;
    email: string;
    role: string;
    profile_photo_url?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    [key: string]: unknown;
};

export type Auth = {
    client_id: any;
    user: User;
    can: {
        impersonate: any;
        view_trainers: any;
        view_students: any;
        create_student: boolean;
    };
};

export type TwoFactorConfigContent = {
    title: string;
    description: string;
    buttonText: string;
};
