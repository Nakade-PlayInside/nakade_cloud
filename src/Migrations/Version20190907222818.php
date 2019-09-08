<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190907222818 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("INSERT INTO `quotes` VALUES 
        (1,'Kämpfen ist nicht der Schlüssel zum Go,\r\nes dient allein als letzter Ausweg.','Zhong-Pu Liu (1078 v. Chr.)'),
        (2,'Go ist ein Glücksspiel, bei dem starke Spieler mehr Glück als Schwächere haben.','Französisches Go-Weisheit'),
        (3,'Go proverbs gelten nicht für Weiß!','Tero Sand'),
        (4,'Halbe Augen gewinnen Semeais.','Bill Taylor'),
        (5,'Gott würfelt nicht mit dem Universum - er spielt Go!','W. Taylor'),
        (6,'Genetisch sind wir alle mehr als 99.9% identisch ; der einzige verbleibende Problem für die zukünftige Menschheit scheint darin zu liegen, wie gut sie Go spielen.','Jumangi'),
        (7,'Setze die Steine nicht wie eine Puppe. Versuche dem Fluß der Steine nachzuspüren.','Fujiwara no Sai (Hikaro no Go)'),
        (8,'Beim Go geht es um die Reihenfolge der Züge.','Rin Kaiho'),
        (9,'Der Lehrer ist die Nadel, der Schüler der Faden.','Miyamoto Musashi'),
        (10,'Gruppen werden nicht getötet. Sie sterben, weil sie nicht verteidigt werden.','James Kerwin'),
        (11,'Die beste Angriffstaktik ist eine gute Verteidigung.','Yang Yilun'),
        (12,'Dein nächster Zug ist wahrscheinlich Sente, wenn du hoffst, dass dein Gegner nicht antwortet.','Jesse'),
        (13,'Der Spieler, der einen Stein in der Hand hält, aber unentschlossen ist, wohin er ihn setzen soll, wird das Spiel nicht gewinnen....','Wei Da Fu'),
        (14,'Bei hoher Vorgabe haben schwarze Steine die Tendenz plötzlich und unerwartet zu sterben.','P. Mioch'),
        (15,'Mauern mögen Ohren haben, aber sie haben keine Augen.','Olawi'),
        (16,'Auf Züge antworten ohne nachzudenken führt zu vielen Niederlagen.','Shi Ding \'an'),
        (17,'Amateure können sich um 5 oder 6 Steine verbessern, aber Profis haben nicht so viel Möglichkeiten für Verbesserungen....','Go Seigen'),
        (18,'Durch das Go-Spiel werden wir gute Freunde.','Takagawa Shukaku Meijin'),
        (19,'Go ist Wissenschaft, Kunst und Spiel. Nur wenige Auserwählte, die wissenschaftliche Präzision, künstlerische Improvisation und die spirituelle Freude des Spiels zusammen meistern, werden in die ewigen Hallen des Ruhmes einziehen.','Kajiwara Takeo (Budapest 1986 EGC)'),
        (20,'Beim Go geht es nicht darum,  durch brilliante Züge zu gewinnen, sondern durch schlechte zu verlieren.','Sakata Eio'),
        (21,'Wenn ein Affe vom Baum fällt, bleibt er immer noch ein Affe. Aber wenn ein Meijin seinen Titel verliert, wird er zu irgendeinem weiteren Go-Spieler.','Nakayama Noriyuki'),
        (22,'Man braucht nur wenig Zeit das Go-Spiel zu lernen, aber eine Lebensspanne um es zu meistern.','traditionell'),
        (23,'Das Brett ist der Spiegel des Geistes und der Momente. Wenn ein Meister das Kifu studiert kann er sagen, zu welchem Zeitpunkt der Schüler von der Gier ergriffen, wann er müde, wann er leichtfertig wurde und wann der Tee serviert wurde.','unbekannt'),
        (24, 'Das Go-Spiel ist so elegant, natürlich und logisch. Sollte es im Universum irgendwo intelligentes Lebensformen geben, so werden sie mit ziemlicher Gewissheit Go spielen.','Emanuel Lasker (1868-1941), Schachweltmeister und Go-Enthusiast'),
        (25,'Ein Meijin braucht kein Joseki.','unbekannt'),
        (26,'Gehe nicht auf die Jagd, wenn dein Haus brennt.',' Go-Weisheit'),
        (27,'Go Spieler sterben und lernen.','unbekannt'),
        (28,'Go verhält sich zu Schach wie Philosophie zu doppelter Buchführung.','unbekannt'),
        (29,'Ich tendiere dazu, Go als eine mentale Form des Kampfsports zu betrachten.','Janice Kim'),
        (30,'Beim Go gibt es keine Bauernopfer.','Steffen Glückselig'),
        (31,'Aggressiv zu spielen ist ein Weg zu lernen, nicht zu gewinnen.','Unbekannt'),
        (32,'Atari spielen und danach zu decken ist oft ein schlechter Stil.',' Go-Weisheit'),
        (33,'Schach gilt als königliches Spiel; Go ist das Spiel der Götter.','Claus Albermann'),
        (34,'Wenn Schach eine Schlacht ist, dann ist Go ein Krieg.','Unbekannt'),
        (35,'Go hat eine durchgehendere Logik als das Schach, ist ihm an Einfachheit überlegen und steht ihm, glaube ich, an Schwung der Phantasie nicht nach.','Emanuel Lasker (1868-1941), Schachweltmeister und Go-Enthusiast'),
        (36,'Alles was du bist, ist in deinem Go-Spiel.','Hotta Yumi')
        ;");
    }

    public function down(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('TRUNCATE `quotes`;');
    }
}
