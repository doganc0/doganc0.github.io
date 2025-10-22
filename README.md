# Magnus Akıllı Teklif Formu

Bu depo, Magnus Sigorta için dinamik olarak yönetilebilen çok adımlı teklif formu ve yönetici panelinin PHP ile geliştirilmiş kaynak kodlarını içerir.

## Özellikler

- Ürün kartlarından başlayan modern ve animasyonlu çok adımlı form deneyimi.
- Sigorta türüne göre adımlar ve soruların MySQL veritabanından dinamik olarak yüklenmesi.
- SweetAlert2 ile AJAX tabanlı form gönderimi ve başarılı başvuru bildirimi.
- Yönetici panelinden logo, avatar, telefon, yönlendirme adresi gibi genel ayarların yönetilmesi.
- Sigorta türleri, form adımları ve sorular için tam CRUD işlemleri.
- Gelen teklifler (lead) ekranı ve ayrıntılı cevap görüntüleme.
- Form gönderimi sonrası veritabanına kayıt ve e-posta bildirimi.

## Kurulum

1. Depoyu kopyalayın ve sunucunuzun kök dizinine yerleştirin. Web sunucunuzun `public/` klasörünü döküman kökü olarak kullanması önerilir.
2. `sql/database.sql` dosyasındaki tabloları MySQL veritabanınıza aktarın. Dosya içinde varsayılan bir yönetici hesabı (`admin` / `admin123`) ve ayarlar bulunmaktadır.
3. `config/app.php` dosyasını düzenleyerek veritabanı bağlantı bilgilerinizi ve e-posta ayarlarınızı güncelleyin.
4. `public/uploads/` klasörüne web sunucusu tarafından yazma izni verin.
5. Yönetici paneline `/admin/login.php` adresinden erişin ve varsayılan kullanıcı adı/şifre ile giriş yaptıktan sonra kendi parolanızı güncelleyin.

## Geliştirme Notları

- Backend PHP 8.x ile OOP ve PDO kullanılarak geliştirilmiştir.
- Form verileri `tbl_submissions` tablosunda JSON olarak saklanır.
- Tasarım Poppins yazı tipi, CSS Grid/Flexbox ve basit animasyonlar ile oluşturulmuştur.
- JavaScript tarafında ES6 sözdizimi kullanılarak dinamik içerik oluşturma, validasyon ve AJAX işlemleri sağlanmıştır.

## Lisans

Bu proje Magnus Sigorta için hazırlanmıştır. İzinsiz kopyalanamaz.
