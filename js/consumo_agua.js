import javax.servlet.*;
import javax.servlet.http.*;
import java.io.IOException;
import java.sql.*;

public class AdminServlet extends HttpServlet {
    protected void doGet(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {
        String pageParam = request.getParameter("page");
        String searchParam = request.getParameter("search");
        int page = Integer.parseInt(pageParam);
        String search = searchParam != null ? searchParam : "";


        String url = "jdbc:mysql://si me puede ayudar conectando esta parte seria genial;
        String usuarioDB = "tu_usuario";
        String contraseñaDB = "tu_contraseña";
        String consultaSQL = "SELECT * FROM usuarios WHERE nombreUsuario LIKE ? OR email LIKE ? LIMIT ?, 10";

        try {
            Connection conexion = DriverManager.getConnection(url, usuarioDB, contraseñaDB);
            PreparedStatement sentencia = conexion.prepareStatement(consultaSQL);

            sentencia.setString(1, "%" + search + "%");
            sentencia.setString(2, "%" + search + "%");
            sentencia.setInt(3, (page - 1) * 10);

            ResultSet resultados = sentencia.executeQuery();

            StringBuilder html = new StringBuilder();
            while (resultados.next()) {
                html.append("<div>").append(resultados.getString("nombreUsuario")).append("</div>");
            }
            response.setContentType("text/html");
            response.getWriter().write(html.toString());
            resultados.close();
            sentencia.close();
            conexion.close();
        } catch (SQLException e) {
            throw new ServletException("Error al conectar con la base de datos", e);
        }
    }
}
