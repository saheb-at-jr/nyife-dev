const axios = require("axios").default;
const mysql = require("mysql2/promise");
const nodemailer = require("nodemailer");

// DB connection config
const target_db_config = {
    host: '193.203.184.176',
    user: 'u627756181_sa',
    password: 'Details@2024',
    database: 'u627756181_masterdatabase'
};

// Month names array
const monthNames = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
];

// Simple HTML template helper
const betterPerformance = (name, criteria) => `
  <div class="tem-body"
    style="max-width: 800px; font-family: 'Poppins', sans-serif !important; display: grid; margin: 0 auto; overflow: hidden; background: url(https://cdn.bitrix24.in/b28507921/sender/a9c/a9c50263ca58dde80980c92e422b2e34/0acc76126ef572299b0efeb1ec68ef9f.jpg);">
    <img src="https://cdn.bitrix24.in/b28507921/sender/12e/12ecd8bbeedd1d13c771fe66385f73c4/08a07a4c8ab4be50efe0feff2e81abe8.png"
        alt style="display: block;width: 40%;margin: 0 auto;">
    <img alt="Main banner"
        src="https://cdn.bitrix24.in/b28507921/disk/b58/b5819c8afee2127dcb24917c727f728f/a214309721d0d74d85bad0e1a142b8ac.png"
        style="width: 80%; margin: 20px auto;">
    <div class="tem-msg-body" style="max-width: 600px;margin: 0 auto;text-align: justify;">
        <div style="padding: 0 30px;" class="tem-msg">
            <h2 style="color: #FFD90F; font-weight: 700; font-size: 25px; margin: 0; letter-spacing: 0.5px;">Dear
                ${name}</h2>
            <p style="font-size: 16px; color: white; margin-bottom: 10px;">
                We've noticed that your performance has not met the expected benchmark this month. As you know, our goal
                is ✦ to achieve at least 95% of the assigned performance
            </p>
            <p style="font-size: 16px; color: white; margin-bottom: 10px;">
                While occasional challenges are understandable, maintaining consistent performance is crucial. <span
                    style="color: #FFD90F;">We encourage open and effective communication, as it allows us to identify
                    and resolve any challenges that may be affecting performance.</span>
            </p>
            <p style="font-size: 16px; color: white; margin-bottom: 10px;">
                Please feel free to share any concerns or areas where you may need support, we're here to help you get
                back on track.
            </p>
            <p style="font-size: 16px; color: white; margin-bottom: 10px;">
                Looking forward to seeing improvements in the coming month!
            </p>
            <p style="font-size: 16px; color: white; margin-bottom: 10px;">
                Best regards,
                <br>
                <strong>
                    Team JR Compliance
                </strong>
            </p>
        </div>
    </div>
    <div class="tem-body-ft"
        style="background: url(https://cdn.bitrix24.in/b28507921/disk/8c0/8c074812e226f02537e2318cfbafe5d5/ca49a63d1fcb62f3f3f245274c5869b1.jpg); max-width: 100%;">
        <div style="text-align: center; padding-top: 130px; padding-bottom: 40px;">
            <img alt="Logo"
                src="https://cdn.bitrix24.in/b28507921/sender/d33/d336851346f111ac5faba0ce658d26eb/2dee7a477b07ee64790eb2d6d6b4bb92.png"
                style="width: 70%; margin-bottom: 0px;">
            <div class="toll-num">
                <a href="tel:7292030466" style="display: inline-block; text-decoration: none;">
                    <div
                        style="display: flex; background-color: #002fa5; justify-content: center; align-items: center;padding: 10px; border-radius: 40px;">
                        <img alt="Phone icon"
                            src="https://cdn.bitrix24.in/b28507921/sender/6b1/6b18594d654758a597be2891c9a79660/abbbd75d50e32103eebe9a5c2e059e20.png"
                            style="width: 35px; margin-right: 0px;">
                        <p style="font-size: 16px; color: #fed810;font-size: 25px; margin:0; margin-left: 10px;">
                            <b>1800-121-410-410</b>
                        </p>
                    </div>
                </a>
            </div>
            <img alt="Divider"
                src="https://cdn.bitrix24.in/b28507921/sender/683/683e9159d7d3eed607d4917c686080d6/dc5336999ca0085c45677af5d9ca5d04.png"
                style="width: 55%; margin: 40px auto;">
            <div
                style="background: url(https://cdn.bitrix24.in/b28507921/sender/c94/c94fca8e8240827bca6817dfbfb2f547/47139edb7634c193cfb90574130f8273.png) no-repeat center; border: none;max-width: 80%;margin: 0 auto;border-radius: 20px; text-align: left; background-size: cover;padding: 1% 2%;">
                <div align="center"
                    style="background-color: #fed810; font-weight: 600;display: flex;align-items: center; font-size: 15px; justify-content: center;padding: 5px 3px;border-radius: 30px; align-items: center;width: 150px;margin: 1px auto;">
                    <img alt="Contact Us Icon"
                        src="https://cdn.bitrix24.in/b28507921/sender/8f9/8f94618a0e3d8866051f332ae518bf77/e8b517b3aac4f9c7449f5329605804d5.png"
                        style="width: 14%; margin-right: 10px;">Contact Us
                </div> <a href="tel:7292030466" style="display: inline-block; text-decoration: none;">
                    <div
                        style="display: flex; justify-content: center; align-items: center;padding: 5px 0px; border-radius: 40px; align-items: center;">
                        <img alt="Phone icon"
                            src="https://cdn.bitrix24.in/b28507921/sender/446/446785af7a13c45099f3a7009858fb8f/fb21abef15707dfad46e86ec4f056df2.png"
                            style="width: 25px;">
                        <p
                            style="font-size: 16px; color: #fff;font-size: 18px; margin:0; margin-left: 5px; align-items: center; display: flex;">
                            1800-121-410-410
                        </p>
                    </div>
                </a>
                <br>
                <a href="https://www.jrcompliance.com/" style="display: inline-block; text-decoration: none;">
                    <div
                        style="display: flex; justify-content: center; align-items: center;padding: 5px 0px; border-radius: 40px; align-items: center;">
                        <img alt="Website icon"
                            src="https://cdn.bitrix24.in/b28507921/sender/b11/b11dac6e41ae8db231816089ff8532fa/fb23adeb4d2a6967c0869bb05cc8d822.png"
                            style="width: 25px;">
                        <p
                            style="font-size: 16px; color: #fff;font-size: 18px; margin:0; margin-left: 5px; align-items: center; display: flex;">
                            www.jrcompliance.com
                        </p>
                    </div>
                </a>
                <br>
                <a href="mailto:support@jrcompliance.com" style="display: inline-block; text-decoration: none;">
                    <div
                        style="display: flex; justify-content: center; align-items: center;padding: 5px 0px; border-radius: 40px; align-items: center;">
                        <img alt="Email icon"
                            src="https://cdn.bitrix24.in/b28507921/sender/7ef/7efdbd75e763689a97cfcd17889fb0d3/7bb899213cafe17d2411f18c8def2094.png"
                            style="width: 25px;">
                        <p
                            style="font-size: 16px; color: #fff;font-size: 18px; margin:0; margin-left: 5px; align-items: center; display: flex;">
                            support@jrcompliance.com
                        </p>
                    </div>
                </a>
                <div
                    style="display: flex; justify-content: center; align-items: center;padding: 5px 0px; border-radius: 40px;">
                    <p
                        style="font-size: 16px; color: #fed810;font-size: 18px; margin:0; margin-left: 15px; display: flex; align-items: center;">
                        Follow us on - <a href="https://www.facebook.com/jrcompliance" style="display: flex;"> <img
                                src="https://cdn.bitrix24.in/b28507921/sender/fc9/fc9d9155f9573fb7aff8192e74ff1e30/43b5db19e18c6ef9151713741c37fe8e.png"
                                style="width: 20px; margin-left: 10px;" alt>
                        </a> <a href="https://www.youtube.com/channel/UCM2rZyfloaxb7q7DdboDNvw" style="display: flex;">
                            <img src="https://cdn.bitrix24.in/b28507921/sender/658/6588a190ab60db73801ce223b14943f2/ecc14ecd736f6922ad8a357204cdaf53.png"
                                style="width: 20px; margin-left: 10px;" alt>
                        </a> <a href="https://in.linkedin.com/company/jr-compliance-&amp;-testing-labs"
                            style="display: flex;"> <img
                                src="https://cdn.bitrix24.in/b28507921/sender/913/913520b32da7e3d48385818537db0cb2/6b5a2bbbba7db46c176a1d3fca98f1a1.png"
                                style="width: 20px; margin-left: 10px;" alt>
                        </a> <a href="https://www.instagram.com/jrcompliance/" style="display: flex;"> <img
                                src="https://cdn.bitrix24.in/b28507921/sender/ab2/ab2fcb946d3253314bfe7da52fca6616/2a51d5d070d7b93fb94511475b6c50cd.png"
                                style="width: 20px; margin-left: 10px;" alt>
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

`;

// 1) Sends one email per (email, criteria, employeeName)
async function sendEmailOnce(emailTo, criteria, employeeName) {
    let conn;
    try {
        // MySQL connection
        conn = await mysql.createConnection(target_db_config);

        // Check if already sent
        const [sent] = await conn.query(
            "SELECT 1 FROM sent_emails WHERE email_to = ? AND criteria = ?",
            [emailTo, criteria]
        );
        if (sent.length) {
            console.log(`⚠️ Already sent to ${emailTo} – (${criteria})`);
            return;
        }

        // Prepare transporter
        // const transporter = nodemailer.createTransport({
        //     host: "send.smtp.com",
        //     port: 465,
        //     secure: true,
        //     auth: {
        //         user: "rkeshmishra@gmail.com",
        //         pass: "kuo6o3jj"
        //     }
        // });
        const transporter = nodemailer.createTransport({
            host: 'notify.jrcompliance.com',
            port: 587,
            secure: false,
            auth: {
                user: 'noreply@jrcompliance.com',
                pass: '53,tYpoSe,_',
            },
            tls: {
                rejectUnauthorized: false,
            },
        });


        // Send email with dynamic HTML
        await transporter.sendMail({
            from: "JR Compliance <noreply@jrcompliance.com>",
            to: emailTo,
            subject: `Notification: ${criteria}`,
            html: betterPerformance(employeeName, criteria)
        });

        // Log it so we don’t send twice
        await conn.query(
            "INSERT INTO sent_emails (email_to, criteria) VALUES (?, ?)",
            [emailTo, criteria]
        );
        // Log success
        console.log(`✅ Sent to ${emailTo} – (${criteria})`);
    } catch (err) {
        console.error("❌ sendEmailOnce error:", err.message);
    } finally {
        if (conn) await conn.end();
    }
}
async function sendEmailOnce2(emailTo, criteria, employeeName) {
    let conn;
    try {
        conn = await mysql.createConnection(target_db_config);

        // Check if already sent
        const [sent] = await conn.query(
            "SELECT 1 FROM sent_emails WHERE email_to = ? AND criteria = ?",
            [emailTo, criteria]
        );
        if (sent.length) {
            console.log(`⚠️ Already sent to ${emailTo} – (${criteria})`);
            return;
        }

        // Prepare transporter
        const transporter = nodemailer.createTransport({
            host: 'notify.jrcompliance.com',
            port: 587,
            secure: false,
            auth: {
                user: 'noreply@jrcompliance.com',
                pass: '53,tYpoSe,_',
            },
            tls: {
                rejectUnauthorized: false,
            },


        });

        // Send email with dynamic HTML
        await transporter.sendMail({
            from: "JR Compliance <noreply@jrcompliance.com>",
            to: emailTo,
            subject: `Notification: ${criteria}`,
            html: success(employeeName, criteria)
        });

        // Log it so we don’t send twice
        await conn.query(
            "INSERT INTO sent_emails (email_to, criteria) VALUES (?, ?)",
            [emailTo, criteria]
        );

        console.log(`✅ Sent to ${emailTo} – (${criteria})`);
    } catch (err) {
        console.error("❌ sendEmailOnce error:", err.message);
    } finally {
        if (conn) await conn.end();
    }
}
async function sendEmailOnce3(emailTo, criteria, employeeName) {
    let conn;
    try {
        conn = await mysql.createConnection(target_db_config);

        // Check if already sent
        const [sent] = await conn.query(
            "SELECT 1 FROM sent_emails WHERE email_to = ? AND criteria = ?",
            [emailTo, criteria]
        );
        if (sent.length) {
            console.log(`⚠️ Already sent to ${emailTo} – (${criteria})`);
            return;
        }

        // Prepare transporter
        const transporter = nodemailer.createTransport({
            host: 'notify.jrcompliance.com',
            port: 587,
            secure: false,
            auth: {
                user: 'noreply@jrcompliance.com',
                pass: '53,tYpoSe,_',
            },
            tls: {
                rejectUnauthorized: false,
            },


        });

        // Send email with dynamic HTML
        await transporter.sendMail({
            from: "JR Compliance <noreply@jrcompliance.com>",
            to: emailTo,
            subject: `Notification: ${criteria}`,
            html: greatWork(employeeName, criteria)
        });

        // Log it so we don’t send twice
        await conn.query(
            "INSERT INTO sent_emails (email_to, criteria) VALUES (?, ?)",
            [emailTo, criteria]
        );

        console.log(`✅ Sent to ${emailTo} – (${criteria})`);
    } catch (err) {
        console.error("❌ sendEmailOnce error:", err.message);
    } finally {
        if (conn) await conn.end();
    }
}
async function sendEmailOnce4(emailTo, criteria, employeeName) {
    let conn;
    try {
        conn = await mysql.createConnection(target_db_config);

        // Check if already sent
        const [sent] = await conn.query(
            "SELECT 1 FROM sent_emails WHERE email_to = ? AND criteria = ?",
            [emailTo, criteria]
        );
        if (sent.length) {
            console.log(`⚠️ Already sent to ${emailTo} – (${criteria})`);
            return;
        }

        // Prepare transporter
        const transporter = nodemailer.createTransport({
            host: 'notify.jrcompliance.com',
            port: 587,
            secure: false,
            auth: {
                user: 'noreply@jrcompliance.com',
                pass: '53,tYpoSe,_',
            },
            tls: {
                rejectUnauthorized: false,
            },


        });

        // Send email with dynamic HTML
        await transporter.sendMail({
            from: "JR Compliance <noreply@jrcompliance.com>",
            to: emailTo,
            subject: `Notification: ${criteria}`,
            html: warning(employeeName, criteria)
        });

        // Log it so we don’t send twice
        await conn.query(
            "INSERT INTO sent_emails (email_to, criteria) VALUES (?, ?)",
            [emailTo, criteria]
        );

        console.log(`✅ Sent to ${emailTo} – (${criteria})`);
    } catch (err) {
        console.error("❌ sendEmailOnce error:", err.message);
    } finally {
        if (conn) await conn.end();
    }
}

// 2) Fetch this month’s records and delegate to sendEmailOnce
async function fetchAndNotify() {
    const now = new Date();
    const currentMonth = monthNames[now.getMonth()];
    console.log("📆 Current month:", currentMonth);

    try {
        const resp = await axios.get(
            "https://table.jrcompliance.com/api/v2/tables/moov5epion9vlp4/records",
            {
                params: {
                    offset: "0",
                    limit: "1000",
                    where: `(Month_Name,eq,${currentMonth})~and(Email to Send (Criteria based),neq,No mail)`,
                    viewId: "vwfgs2x1uv9zae6s"
                },
                headers: { "xc-token": "rtHGPnwISVx8zbHQG9PGEG22Fg6eu55FfR5uOvGm" }
            }
        );

        console.log("✅ Data fetched at", new Date().toLocaleTimeString());
        const list = resp.data.list;
        if (!Array.isArray(list)) {
            console.error("❌ Expected an array at response.data.list; got:", typeof list);
            return;
        }

        for (const record of list) {
            const email = record.Email;
            const criteria = record["Email to Send (Criteria based)"];
            const employeeName = record.Employee_Name;

            // Only trigger for this specific criteria
            if (email && criteria === "Eligible for Improvement mail") {
                await sendEmailOnce(email, criteria, employeeName);
            } else if (email && criteria === "Congratulations Mail") {
                await sendEmailOnce2(email, criteria, employeeName);
            }
            else if (email && criteria === "Eligible for Gratitude Mail") {
                await sendEmailOnce3(email, criteria, employeeName);
            }
            else if (email && criteria === "Warning mail") {
                await sendEmailOnce4(email, criteria, employeeName);
            }
            else {
                console.warn("⚠️ Skipping record:", record);
            }
        }
    } catch (err) {
        console.error("❌ fetch error:", err.response?.data || err.message);
    }
}

// Kick it off
setInterval(fetchAndNotify, 30 * 1000);


const success = (name, criteria) =>
    `
<div class="tem-body"
    style="max-width: 800px; font-family: 'Poppins', sans-serif !important; display: grid; margin: 0 auto; overflow: hidden; background: url(https://cdn.bitrix24.in/b28507921/sender/a9c/a9c50263ca58dde80980c92e422b2e34/0acc76126ef572299b0efeb1ec68ef9f.jpg);">
    <img src="https://cdn.bitrix24.in/b28507921/sender/12e/12ecd8bbeedd1d13c771fe66385f73c4/08a07a4c8ab4be50efe0feff2e81abe8.png"
        alt style="display: block;width: 40%;margin: 0 auto;">
    <img alt="Main banner"
        src="https://cdn.bitrix24.in/b28507921/disk/023/023463422e015ccd5a9d43f1f4da5324/5af818f2ad3dcfc17a7200464db32328.png"
        style="width: 80%; margin: 20px auto;">
    <div class="tem-msg-body" style="max-width: 600px;margin: 0 auto;text-align: justify;">
        <div style="padding: 0 30px;" class="tem-msg">
            <h2 style="color: #FFD90F; font-weight: 700; font-size: 25px; margin: 0; letter-spacing: 0.5px;">Dear
                ${name}</h2>
            <p style="font-size: 16px; color: white; margin-bottom: 10px;">
                We're delighted to recognize your outstanding efforts this month! Your dedication and hard work have
                truly set a benchmark, and we're proud to announce you as the Employee of the Month.
            </p>
            <p style="font-size: 16px; color: white; margin-bottom: 10px;">
                Your performance has not only exceeded expectations but also served as an inspiration to your
                colleagues. Your proactive approach, problem-solving skills, and
                consistent drive have significantly contributed to the team's success.
            </p>
            <p style="font-size: 16px; color: white; margin-bottom: 10px;">
                Keep up the great work, and thank you for your
                invaluable contributions to our team!
            </p>
            <p style="font-size: 16px; color: white; margin-bottom: 10px;">
                Best regards,
                <br>
                <strong>
                    Team JR Compliance
                </strong>
            </p>
        </div>
    </div>
    <div class="tem-body-ft"
        style="background: url(https://cdn.bitrix24.in/b28507921/disk/8c0/8c074812e226f02537e2318cfbafe5d5/ca49a63d1fcb62f3f3f245274c5869b1.jpg); max-width: 100%;">
        <div style="text-align: center; padding-top: 130px; padding-bottom: 40px;">
            <img alt="Logo"
                src="https://cdn.bitrix24.in/b28507921/sender/d33/d336851346f111ac5faba0ce658d26eb/2dee7a477b07ee64790eb2d6d6b4bb92.png"
                style="width: 70%; margin-bottom: 0px;">
            <div class="toll-num">
                <a href="tel:7292030466" style="display: inline-block; text-decoration: none;">
                    <div
                        style="display: flex; background-color: #002fa5; justify-content: center; align-items: center;padding: 10px; border-radius: 40px;">
                        <img alt="Phone icon"
                            src="https://cdn.bitrix24.in/b28507921/sender/6b1/6b18594d654758a597be2891c9a79660/abbbd75d50e32103eebe9a5c2e059e20.png"
                            style="width: 35px; margin-right: 0px;">
                        <p style="font-size: 16px; color: #fed810;font-size: 25px; margin:0; margin-left: 10px;">
                            <b>1800-121-410-410</b>
                        </p>
                    </div>
                </a>
            </div>
            <img alt="Divider"
                src="https://cdn.bitrix24.in/b28507921/sender/683/683e9159d7d3eed607d4917c686080d6/dc5336999ca0085c45677af5d9ca5d04.png"
                style="width: 55%; margin: 40px auto;">
            <div
                style="background: url(https://cdn.bitrix24.in/b28507921/sender/c94/c94fca8e8240827bca6817dfbfb2f547/47139edb7634c193cfb90574130f8273.png) no-repeat center; border: none;max-width: 80%;margin: 0 auto;border-radius: 20px; text-align: left; background-size: cover;padding: 1% 2%;">
                <div align="center"
                    style="background-color: #fed810; font-weight: 600;display: flex;align-items: center; font-size: 15px; justify-content: center;padding: 5px 3px;border-radius: 30px; align-items: center;width: 150px;margin: 1px auto;">
                    <img alt="Contact Us Icon"
                        src="https://cdn.bitrix24.in/b28507921/sender/8f9/8f94618a0e3d8866051f332ae518bf77/e8b517b3aac4f9c7449f5329605804d5.png"
                        style="width: 14%; margin-right: 10px;">Contact Us
                </div> <a href="tel:7292030466" style="display: inline-block; text-decoration: none;">
                    <div
                        style="display: flex; justify-content: center; align-items: center;padding: 5px 0px; border-radius: 40px; align-items: center;">
                        <img alt="Phone icon"
                            src="https://cdn.bitrix24.in/b28507921/sender/446/446785af7a13c45099f3a7009858fb8f/fb21abef15707dfad46e86ec4f056df2.png"
                            style="width: 25px;">
                        <p
                            style="font-size: 16px; color: #fff;font-size: 18px; margin:0; margin-left: 5px; align-items: center; display: flex;">
                            1800-121-410-410
                        </p>
                    </div>
                </a>
                <br>
                <a href="https://www.jrcompliance.com/" style="display: inline-block; text-decoration: none;">
                    <div
                        style="display: flex; justify-content: center; align-items: center;padding: 5px 0px; border-radius: 40px; align-items: center;">
                        <img alt="Website icon"
                            src="https://cdn.bitrix24.in/b28507921/sender/b11/b11dac6e41ae8db231816089ff8532fa/fb23adeb4d2a6967c0869bb05cc8d822.png"
                            style="width: 25px;">
                        <p
                            style="font-size: 16px; color: #fff;font-size: 18px; margin:0; margin-left: 5px; align-items: center; display: flex;">
                            www.jrcompliance.com
                        </p>
                    </div>
                </a>
                <br>
                <a href="mailto:support@jrcompliance.com" style="display: inline-block; text-decoration: none;">
                    <div
                        style="display: flex; justify-content: center; align-items: center;padding: 5px 0px; border-radius: 40px; align-items: center;">
                        <img alt="Email icon"
                            src="https://cdn.bitrix24.in/b28507921/sender/7ef/7efdbd75e763689a97cfcd17889fb0d3/7bb899213cafe17d2411f18c8def2094.png"
                            style="width: 25px;">
                        <p
                            style="font-size: 16px; color: #fff;font-size: 18px; margin:0; margin-left: 5px; align-items: center; display: flex;">
                            support@jrcompliance.com
                        </p>
                    </div>
                </a>
                <div
                    style="display: flex; justify-content: center; align-items: center;padding: 5px 0px; border-radius: 40px;">
                    <p
                        style="font-size: 16px; color: #fed810;font-size: 18px; margin:0; margin-left: 15px; display: flex; align-items: center;">
                        Follow us on - <a href="https://www.facebook.com/jrcompliance" style="display: flex;"> <img
                                src="https://cdn.bitrix24.in/b28507921/sender/fc9/fc9d9155f9573fb7aff8192e74ff1e30/43b5db19e18c6ef9151713741c37fe8e.png"
                                style="width: 20px; margin-left: 10px;" alt>
                        </a> <a href="https://www.youtube.com/channel/UCM2rZyfloaxb7q7DdboDNvw" style="display: flex;">
                            <img src="https://cdn.bitrix24.in/b28507921/sender/658/6588a190ab60db73801ce223b14943f2/ecc14ecd736f6922ad8a357204cdaf53.png"
                                style="width: 20px; margin-left: 10px;" alt>
                        </a> <a href="https://in.linkedin.com/company/jr-compliance-&amp;-testing-labs"
                            style="display: flex;"> <img
                                src="https://cdn.bitrix24.in/b28507921/sender/913/913520b32da7e3d48385818537db0cb2/6b5a2bbbba7db46c176a1d3fca98f1a1.png"
                                style="width: 20px; margin-left: 10px;" alt>
                        </a> <a href="https://www.instagram.com/jrcompliance/" style="display: flex;"> <img
                                src="https://cdn.bitrix24.in/b28507921/sender/ab2/ab2fcb946d3253314bfe7da52fca6616/2a51d5d070d7b93fb94511475b6c50cd.png"
                                style="width: 20px; margin-left: 10px;" alt>
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
`;
const greatWork = (name, criteria) =>
    `
<div class="tem-body"
    style="max-width: 800px; font-family: 'Poppins', sans-serif !important; display: grid; margin: 0 auto; overflow: hidden; background: url(https://cdn.bitrix24.in/b28507921/sender/a9c/a9c50263ca58dde80980c92e422b2e34/0acc76126ef572299b0efeb1ec68ef9f.jpg);">
    <img src="https://cdn.bitrix24.in/b28507921/sender/12e/12ecd8bbeedd1d13c771fe66385f73c4/08a07a4c8ab4be50efe0feff2e81abe8.png"
        alt style="display: block;width: 40%;margin: 0 auto;">
    <img alt="Main banner"
        src="https://cdn.bitrix24.in/b28507921/disk/212/212552d96d9d108955544d270d9c6a1b/4026149a64d5a20248cdec20467a017f.png"
        style="width: 80%; margin: 20px auto;">
    <div class="tem-msg-body" style="max-width: 600px;margin: 0 auto;text-align: justify;">
        <div style="padding: 0 30px;" class="tem-msg">
            <h2 style="color: #FFD90F; font-weight: 700; font-size: 25px; margin: 0; letter-spacing: 0.5px;">Dear
                ${name}</h2>
            <p style="font-size: 16px; color: white; margin-bottom: 10px;">
                We want to take a moment to recognize and appreciate your outstanding performance this month. Your
                dedication, commitment, and consistency have played a significant role in driving our team forward.
                Achieving a performance level of over 95% is no small feat, and it reflects your strong work ethic and
                determination.
            </p>
            <p style="font-size: 16px; color: white; margin-bottom: 10px;">
                While the Employee of the Month title is reserved for one individual, excellence is never limited to
                just one name. Your contributions are valued, and your efforts have set a high standard for everyone
                around you. Keep up the fantastic work, and know that your hard work does not go unnoticed. We look
                forward to seeing your continued success in the coming months!
            </p>
            <p style="font-size: 16px; color: white; margin-bottom: 10px;">
                Best regards,
                <br>
                <strong>
                    Team JR Compliance
                </strong>
            </p>
        </div>
    </div>
    <div class="tem-body-ft"
        style="background: url(https://cdn.bitrix24.in/b28507921/disk/8c0/8c074812e226f02537e2318cfbafe5d5/ca49a63d1fcb62f3f3f245274c5869b1.jpg); max-width: 100%;">
        <div style="text-align: center; padding-top: 130px; padding-bottom: 40px;">
            <img alt="Logo"
                src="https://cdn.bitrix24.in/b28507921/sender/d33/d336851346f111ac5faba0ce658d26eb/2dee7a477b07ee64790eb2d6d6b4bb92.png"
                style="width: 70%; margin-bottom: 0px;">
            <div class="toll-num">
                <a href="tel:7292030466" style="display: inline-block; text-decoration: none;">
                    <div
                        style="display: flex; background-color: #002fa5; justify-content: center; align-items: center;padding: 10px; border-radius: 40px;">
                        <img alt="Phone icon"
                            src="https://cdn.bitrix24.in/b28507921/sender/6b1/6b18594d654758a597be2891c9a79660/abbbd75d50e32103eebe9a5c2e059e20.png"
                            style="width: 35px; margin-right: 0px;">
                        <p style="font-size: 16px; color: #fed810;font-size: 25px; margin:0; margin-left: 10px;">
                            <b>1800-121-410-410</b>
                        </p>
                    </div>
                </a>
            </div>
            <img alt="Divider"
                src="https://cdn.bitrix24.in/b28507921/sender/683/683e9159d7d3eed607d4917c686080d6/dc5336999ca0085c45677af5d9ca5d04.png"
                style="width: 55%; margin: 40px auto;">
            <div
                style="background: url(https://cdn.bitrix24.in/b28507921/sender/c94/c94fca8e8240827bca6817dfbfb2f547/47139edb7634c193cfb90574130f8273.png) no-repeat center; border: none;max-width: 80%;margin: 0 auto;border-radius: 20px; text-align: left; background-size: cover;padding: 1% 2%;">
                <div align="center"
                    style="background-color: #fed810; font-weight: 600;display: flex;align-items: center; font-size: 15px; justify-content: center;padding: 5px 3px;border-radius: 30px; align-items: center;width: 150px;margin: 1px auto;">
                    <img alt="Contact Us Icon"
                        src="https://cdn.bitrix24.in/b28507921/sender/8f9/8f94618a0e3d8866051f332ae518bf77/e8b517b3aac4f9c7449f5329605804d5.png"
                        style="width: 14%; margin-right: 10px;">Contact Us
                </div> <a href="tel:7292030466" style="display: inline-block; text-decoration: none;">
                    <div
                        style="display: flex; justify-content: center; align-items: center;padding: 5px 0px; border-radius: 40px; align-items: center;">
                        <img alt="Phone icon"
                            src="https://cdn.bitrix24.in/b28507921/sender/446/446785af7a13c45099f3a7009858fb8f/fb21abef15707dfad46e86ec4f056df2.png"
                            style="width: 25px;">
                        <p
                            style="font-size: 16px; color: #fff;font-size: 18px; margin:0; margin-left: 5px; align-items: center; display: flex;">
                            1800-121-410-410
                        </p>
                    </div>
                </a>
                <br>
                <a href="https://www.jrcompliance.com/" style="display: inline-block; text-decoration: none;">
                    <div
                        style="display: flex; justify-content: center; align-items: center;padding: 5px 0px; border-radius: 40px; align-items: center;">
                        <img alt="Website icon"
                            src="https://cdn.bitrix24.in/b28507921/sender/b11/b11dac6e41ae8db231816089ff8532fa/fb23adeb4d2a6967c0869bb05cc8d822.png"
                            style="width: 25px;">
                        <p
                            style="font-size: 16px; color: #fff;font-size: 18px; margin:0; margin-left: 5px; align-items: center; display: flex;">
                            www.jrcompliance.com
                        </p>
                    </div>
                </a>
                <br>
                <a href="mailto:support@jrcompliance.com" style="display: inline-block; text-decoration: none;">
                    <div
                        style="display: flex; justify-content: center; align-items: center;padding: 5px 0px; border-radius: 40px; align-items: center;">
                        <img alt="Email icon"
                            src="https://cdn.bitrix24.in/b28507921/sender/7ef/7efdbd75e763689a97cfcd17889fb0d3/7bb899213cafe17d2411f18c8def2094.png"
                            style="width: 25px;">
                        <p
                            style="font-size: 16px; color: #fff;font-size: 18px; margin:0; margin-left: 5px; align-items: center; display: flex;">
                            support@jrcompliance.com
                        </p>
                    </div>
                </a>
                <div
                    style="display: flex; justify-content: center; align-items: center;padding: 5px 0px; border-radius: 40px;">
                    <p
                        style="font-size: 16px; color: #fed810;font-size: 18px; margin:0; margin-left: 15px; display: flex; align-items: center;">
                        Follow us on - <a href="https://www.facebook.com/jrcompliance" style="display: flex;"> <img
                                src="https://cdn.bitrix24.in/b28507921/sender/fc9/fc9d9155f9573fb7aff8192e74ff1e30/43b5db19e18c6ef9151713741c37fe8e.png"
                                style="width: 20px; margin-left: 10px;" alt>
                        </a> <a href="https://www.youtube.com/channel/UCM2rZyfloaxb7q7DdboDNvw" style="display: flex;">
                            <img src="https://cdn.bitrix24.in/b28507921/sender/658/6588a190ab60db73801ce223b14943f2/ecc14ecd736f6922ad8a357204cdaf53.png"
                                style="width: 20px; margin-left: 10px;" alt>
                        </a> <a href="https://in.linkedin.com/company/jr-compliance-&amp;-testing-labs"
                            style="display: flex;"> <img
                                src="https://cdn.bitrix24.in/b28507921/sender/913/913520b32da7e3d48385818537db0cb2/6b5a2bbbba7db46c176a1d3fca98f1a1.png"
                                style="width: 20px; margin-left: 10px;" alt>
                        </a> <a href="https://www.instagram.com/jrcompliance/" style="display: flex;"> <img
                                src="https://cdn.bitrix24.in/b28507921/sender/ab2/ab2fcb946d3253314bfe7da52fca6616/2a51d5d070d7b93fb94511475b6c50cd.png"
                                style="width: 20px; margin-left: 10px;" alt>
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
`;
const warning = (name, criteria) =>
    `
<div class="tem-body"
    style="max-width: 800px; font-family: 'Poppins', sans-serif !important; display: grid; margin: 0 auto; overflow: hidden; background: url(https://cdn.bitrix24.in/b28507921/sender/a9c/a9c50263ca58dde80980c92e422b2e34/0acc76126ef572299b0efeb1ec68ef9f.jpg);">
    <img src="https://cdn.bitrix24.in/b28507921/sender/12e/12ecd8bbeedd1d13c771fe66385f73c4/08a07a4c8ab4be50efe0feff2e81abe8.png"
        alt style="display: block;width: 40%;margin: 0 auto;">
    <img alt="Main banner"
        src="https://cdn.bitrix24.in/b28507921/disk/3e3/3e3fd10b53dab3952e6cd70534897fe0/0170432d2c8b9d49c9970525be098b5e.png"
        style="width: 80%; margin: 20px auto;">
    <div class="tem-msg-body" style="max-width: 600px;margin: 0 auto;text-align: justify;">
        <div style="padding: 0 30px;" class="tem-msg">
            <h2 style="color: #FFD90F; font-weight: 700; font-size: 25px; margin: 0; letter-spacing: 0.5px;">Dear
                ${name}</h2>
            <p style="font-size: 16px; color: white; margin-bottom: 10px;">
                We have noticed a significant decline in your
                performance this month. As you know, maintaining a minimum of 60% is essential, and your performance
                currently stands below 60%.
            </p>
            <p style="font-size: 16px; color: white; margin-bottom: 10px;">
                This requires immediate attention, as consistent underperformance can impact overall team progress. We urge you to assess the challenges you are facing and take corrective measures. If you require any assistance or guidance, please reach out at the earliest.
            </p>
            <p style="font-size: 16px; color: white; margin-bottom: 10px;">
                Failure to improve in the coming months may result in further action. We hope to see a positive change soon.
            </p>
            <p style="font-size: 16px; color: white; margin-bottom: 10px;">
                Best regards,
                <br>
                <strong>
                    Team JR Compliance
                </strong>
            </p>
        </div>
    </div>
    <div class="tem-body-ft"
        style="background: url(https://cdn.bitrix24.in/b28507921/disk/8c0/8c074812e226f02537e2318cfbafe5d5/ca49a63d1fcb62f3f3f245274c5869b1.jpg); max-width: 100%;">
        <div style="text-align: center; padding-top: 130px; padding-bottom: 40px;">
            <img alt="Logo"
                src="https://cdn.bitrix24.in/b28507921/sender/d33/d336851346f111ac5faba0ce658d26eb/2dee7a477b07ee64790eb2d6d6b4bb92.png"
                style="width: 70%; margin-bottom: 0px;">
            <div class="toll-num">
                <a href="tel:7292030466" style="display: inline-block; text-decoration: none;">
                    <div
                        style="display: flex; background-color: #002fa5; justify-content: center; align-items: center;padding: 10px; border-radius: 40px;">
                        <img alt="Phone icon"
                            src="https://cdn.bitrix24.in/b28507921/sender/6b1/6b18594d654758a597be2891c9a79660/abbbd75d50e32103eebe9a5c2e059e20.png"
                            style="width: 35px; margin-right: 0px;">
                        <p style="font-size: 16px; color: #fed810;font-size: 25px; margin:0; margin-left: 10px;">
                            <b>1800-121-410-410</b>
                        </p>
                    </div>
                </a>
            </div>
            <img alt="Divider"
                src="https://cdn.bitrix24.in/b28507921/sender/683/683e9159d7d3eed607d4917c686080d6/dc5336999ca0085c45677af5d9ca5d04.png"
                style="width: 55%; margin: 40px auto;">
            <div
                style="background: url(https://cdn.bitrix24.in/b28507921/sender/c94/c94fca8e8240827bca6817dfbfb2f547/47139edb7634c193cfb90574130f8273.png) no-repeat center; border: none;max-width: 80%;margin: 0 auto;border-radius: 20px; text-align: left; background-size: cover;padding: 1% 2%;">
                <div align="center"
                    style="background-color: #fed810; font-weight: 600;display: flex;align-items: center; font-size: 15px; justify-content: center;padding: 5px 3px;border-radius: 30px; align-items: center;width: 150px;margin: 1px auto;">
                    <img alt="Contact Us Icon"
                        src="https://cdn.bitrix24.in/b28507921/sender/8f9/8f94618a0e3d8866051f332ae518bf77/e8b517b3aac4f9c7449f5329605804d5.png"
                        style="width: 14%; margin-right: 10px;">Contact Us
                </div> <a href="tel:7292030466" style="display: inline-block; text-decoration: none;">
                    <div
                        style="display: flex; justify-content: center; align-items: center;padding: 5px 0px; border-radius: 40px; align-items: center;">
                        <img alt="Phone icon"
                            src="https://cdn.bitrix24.in/b28507921/sender/446/446785af7a13c45099f3a7009858fb8f/fb21abef15707dfad46e86ec4f056df2.png"
                            style="width: 25px;">
                        <p
                            style="font-size: 16px; color: #fff;font-size: 18px; margin:0; margin-left: 5px; align-items: center; display: flex;">
                            1800-121-410-410
                        </p>
                    </div>
                </a>
                <br>
                <a href="https://www.jrcompliance.com/" style="display: inline-block; text-decoration: none;">
                    <div
                        style="display: flex; justify-content: center; align-items: center;padding: 5px 0px; border-radius: 40px; align-items: center;">
                        <img alt="Website icon"
                            src="https://cdn.bitrix24.in/b28507921/sender/b11/b11dac6e41ae8db231816089ff8532fa/fb23adeb4d2a6967c0869bb05cc8d822.png"
                            style="width: 25px;">
                        <p
                            style="font-size: 16px; color: #fff;font-size: 18px; margin:0; margin-left: 5px; align-items: center; display: flex;">
                            www.jrcompliance.com
                        </p>
                    </div>
                </a>
                <br>
                <a href="mailto:support@jrcompliance.com" style="display: inline-block; text-decoration: none;">
                    <div
                        style="display: flex; justify-content: center; align-items: center;padding: 5px 0px; border-radius: 40px; align-items: center;">
                        <img alt="Email icon"
                            src="https://cdn.bitrix24.in/b28507921/sender/7ef/7efdbd75e763689a97cfcd17889fb0d3/7bb899213cafe17d2411f18c8def2094.png"
                            style="width: 25px;">
                        <p
                            style="font-size: 16px; color: #fff;font-size: 18px; margin:0; margin-left: 5px; align-items: center; display: flex;">
                            support@jrcompliance.com
                        </p>
                    </div>
                </a>
                <div
                    style="display: flex; justify-content: center; align-items: center;padding: 5px 0px; border-radius: 40px;">
                    <p
                        style="font-size: 16px; color: #fed810;font-size: 18px; margin:0; margin-left: 15px; display: flex; align-items: center;">
                        Follow us on - <a href="https://www.facebook.com/jrcompliance" style="display: flex;"> <img
                                src="https://cdn.bitrix24.in/b28507921/sender/fc9/fc9d9155f9573fb7aff8192e74ff1e30/43b5db19e18c6ef9151713741c37fe8e.png"
                                style="width: 20px; margin-left: 10px;" alt>
                        </a> <a href="https://www.youtube.com/channel/UCM2rZyfloaxb7q7DdboDNvw" style="display: flex;">
                            <img src="https://cdn.bitrix24.in/b28507921/sender/658/6588a190ab60db73801ce223b14943f2/ecc14ecd736f6922ad8a357204cdaf53.png"
                                style="width: 20px; margin-left: 10px;" alt>
                        </a> <a href="https://in.linkedin.com/company/jr-compliance-&amp;-testing-labs"
                            style="display: flex;"> <img
                                src="https://cdn.bitrix24.in/b28507921/sender/913/913520b32da7e3d48385818537db0cb2/6b5a2bbbba7db46c176a1d3fca98f1a1.png"
                                style="width: 20px; margin-left: 10px;" alt>
                        </a> <a href="https://www.instagram.com/jrcompliance/" style="display: flex;"> <img
                                src="https://cdn.bitrix24.in/b28507921/sender/ab2/ab2fcb946d3253314bfe7da52fca6616/2a51d5d070d7b93fb94511475b6c50cd.png"
                                style="width: 20px; margin-left: 10px;" alt>
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>`