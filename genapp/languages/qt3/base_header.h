#ifndef DEFINED___application__
#define DEFINED___application__

#include <qapplication.h>
#include <qlayout.h>
#include <qlabel.h>
#include <qpixmap.h>
#include <qimage.h>
#include <qmessagebox.h>
#include <qmap.h>
#include <qvaluevector.h>
#include <qpainter.h>
#include <qpushbutton.h>
#include <qlineedit.h>
#include <qvalidator.h>
#include <qcheckbox.h>
#include <qregexp.h>
#include <qvariant.h>
#include <qprocess.h>
#include <qwt_plot.h>
#include <qcursor.h>
#include <qfileinfo.h>
#include <qfiledialog.h>
#include <qstringlist.h>
#include <qradiobutton.h>
#include <qbuttongroup.h>
#include <qlistbox.h>
#include <qtextedit.h>
#include <qmessagebox.h>
#include <qprocess.h>

#include "utility_routines.h"

class mQLabel : public QLabel
{
   Q_OBJECT

   public:

      mQLabel ( QWidget *parent = 0 , const char * name = 0 );
      mQLabel ( const QString & text, QWidget *parent = 0 , const char * name = 0 );
      ~mQLabel();

      mQLabel * mbuddy;
      QPixmap * pixmap_base;
      QPixmap * pixmap_hover;

   signals:
      void pressed();

   protected:
      virtual void mousePressEvent ( QMouseEvent *e );
      virtual void enterEvent      ( QEvent *e );
      virtual void leaveEvent      ( QEvent *e );
};


class mQPushButton : public QPushButton
{
   Q_OBJECT

   public:

      mQPushButton  ( QWidget *parent = 0 , const char * name = 0 );
      ~mQPushButton ();
      QLabel *      mbuddy;
      QVariant      data;

   protected:
      virtual void enterEvent      ( QEvent *e );
      virtual void leaveEvent      ( QEvent *e );
};

class mQRadioButton : public QRadioButton
{
   Q_OBJECT

   public:

      mQRadioButton  ( QWidget *parent = 0 , const char * name = 0 );
      ~mQRadioButton ();

      QVariant      data;
};

class __application__ : public QFrame
{
   Q_OBJECT

   public:

      __application__();

   private:

      QMap < QString, QPixmap >     id_to_icon;
      QMap < QString, QString >     id_to_label;
      QValueVector < QWidget * >    menu_widgets;
      void                          hide_widgets( QValueVector < QWidget * > &widgets, bool hide = true );
      QValueVector < QWidget * >    panel1_widgets;
      QValueVector < QLayout * >    panel1_layouts;
      QValueVector < QWidget * >    panel1_sub_widgets;
      QValueVector < QLayout * >    panel1_sub_layouts;
      void                          delete_widgets_layouts( QValueVector < QWidget * > &widgets,
                                                            QValueVector < QLayout * > &layouts );

      mQLabel *                     menu_button;
      QGridLayout *                 gl_panel1;
      QGridLayout *                 gl_footer;

      QPalette *                    palette_le;
      QPalette *                    palette_cb;
      QPalette *                    palette_lbl_error;
      QPalette *                    palette_plot;

      QMap < QString, QVariant >    global_data;
      QMap < QString, QString >     global_data_types;

      QValueVector < QString >      panel1_inputs;
      QValueVector < QString >      panel1_outputs;
      QMap < QString, bool >        panel1_is_input;
      QMap < QString, QWidget * >   panel1_widget_map;
      QMap < QString, QString >     panel1_map_input;

      void                          save_value          ( const QString & id, const QString & ext );
      void                          save_last_value     ( const QString & id );
      void                          save_default_value  ( const QString & id );
      void                          save_last_values    ();

      void                          reset_value         ( const QString & id, const QString & ext );
      void                          reset_values        ( const QString & ext );
      void                          reset_output_values ( const QString & ext );
      void                          reset_default_values();
      void                          reset_last_values   ();

      QString                       get_last_value      ( const QString & id , bool & skip );
      QString                       input_to_json       ( const QString & mod, const QString & dir = "" );

      QString                       current_module_id;
      QMap < QString, QString >     process_json;

      void                          process_results     ( const QString & mod );
      QValueVector < QColor >       plot_colors;
      void                          push_back_color_if_ok( QColor bg, QColor set );

      void                          browse_filenames( const QString & label,
                                                      const QString & id,
                                                      bool  multiple_files = false );
      void                          spawn_app( const QString & appname, const QString & filename );

   private slots:

      void                          menu_pressed();
