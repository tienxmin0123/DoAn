import React, { useEffect, useState,useMemo } from "react";
import Footer from "../../Component/Footer/Footer";
import Header from "../../Component/Header/Header";
import "./PaymentResult.css";
import { BrowserRouter as Router, Link, useLocation } from "react-router-dom";
import axios from "axios";
import NumberFormat from "react-number-format";
const orderInfo = JSON.parse(localStorage.getItem("Order") || "[]");

function PaymentResult(){
  const [order, setOder] = useState([]);
  const [statusOrder,setStatusOrder]=useState("");

  function useQuery() {
    return new URLSearchParams(useLocation().search);
  }

  //localMessage
  //Thành công
  //Đơn hàng đã bị huỷ bỏ
  const data = {
    status: 2,
  };
 
  var query = useQuery();
  var name = query.get("localMessage");
  var message = query.get("message");
  var response_code=query.get("vnp_ResponseCode");
  useMemo(() => {
    if (name == "Thành công") {
      axios
        .put(`http://127.0.0.1:8000/api/updateOrder/${orderInfo.id}`, data)
        .then((res) => {
          console.log(res.data);
        });
        setStatusOrder("Đơn hàng thành công");

    } else if (message == 1) {
      axios
        .put(`http://127.0.0.1:8000/api/updateOrder/${orderInfo.id}`, data)
        .then((res) => {
          // localStorage.removeItem("Order");
          console.log(res.data);
        });
      name = "Đơn hàng thành công";
      setStatusOrder("Đơn hàng thành công");
    }
    else if(response_code =="00")
    {
      axios
      .put(`http://127.0.0.1:8000/api/updateOrder/${orderInfo.id}`, data)
      .then((res) => {
        // localStorage.removeItem("Order");
        console.log(res.data);
      });
    setStatusOrder("Đơn hàng thành công");
    }
     else if (message == 2) {
      axios
      .put(`http://127.0.0.1:8000/api/updateOrderCanceled/${orderInfo.id}` )
      .then((res) => {
        // localStorage.removeItem("Order");
        console.log(res.data);
      });
      name = "Đơn hàng bị hủy";
      console.log("paypal");
      setStatusOrder("Đơn hàng bị hủy");

    } 
    else if(message == 3){
      const data_1 ={
        status: 1,
      }
      axios
      .put(`http://127.0.0.1:8000/api/updateOrder/${orderInfo.id}`,data_1 )
      .then((res) => {
        // localStorage.removeItem("Order");
        console.log(res.data);
      });
      name = "Đơn hàng thành công";
      setStatusOrder("Đơn hàng thành công");
    }
    else {
      axios
      .put(`http://127.0.0.1:8000/api/updateOrderCanceled/${orderInfo.id}`)
      .then((res) => {
        // localStorage.removeItem("Order");
        console.log(res.data);
        console.log("momo");
      });
      name = "Đơn hàng bị hủy";
      setStatusOrder("Đơn hàng bị hủy");

    }
  }, []);
  
  useEffect(() => {
    axios
      .get(`http://127.0.0.1:8000/api/getInformationOrderById/${orderInfo.id}`)
      .then((res) => {
        setOder(res.data);
      });
  }, []);
  return (
    <>
      <Header />
      <div className="noindex">
        {order.map((item, index) => (
          <div className="container" key={index}>
            <div className="order-Detail">
              <div className="Heading">
                <span>Chi tiết đơn hàng #HD{item.id} </span>
                <span className="split">-</span>
                <span className="status">
                  <i className="fal fa-check-circle"></i>
                  {statusOrder}
                </span>
              </div>
              {/* <div className="totalPrice">Tổng giá: {item.Tongtien} VNĐ</div> */}
              <NumberFormat
                      value={item.Tongtien}
                      displayType={"text"}
                      thousandSeparator={true}
                      suffix={" VNĐ"}
                      renderText={(value, props) => (
                        <div className="totalPrice" {...props}>
                         Tổng tiền: {value}
                        </div>
                      )}
                    />
              <div className="created-date">
                Ngày đặt hàng: {item.ThoiGianMua}
              </div>

              <div className="information-User">
                <div className="address-User">
                  <div className="title">Địa chỉ người nhận</div>
                  <div className="content">
                    <p className="name">{item.TenNguoidung}</p>
                    <p className="address">
                      <span>Địa chỉ:</span>
                      {item.DiaChi}
                    </p>
                    <p className="phone">
                      <span>Số điện thoại: </span>
                      {item.SDT}
                    </p>
                  </div>
                </div>
                <div className="address-User">
                  <div className="title">Hình thức vận chuyển</div>
                  <div className="content">
                    <p className="name">{item.TenHinhThuc}</p>

                    <p className="phone">
                      <span>Phí vận chuyển: 0đ</span>
                    </p>
                  </div>
                </div>
                <div className="address-User">
                  <div className="title">Hình thức thanh toán</div>
                  <div className="content">
                    <p className="name">{item.TenThanhToan}</p>
                  </div>
                </div>
              </div>
              <Link to="/account-order" className="view-list-order">
                <i className="far fa-arrow-left"></i>
                <span className="backOrder">Quay lại đơn hàng của tôi</span>
              </Link>
              <Link
                to={`/account/order/${orderInfo.id}`}
                className="view-tracking-detail"
              >
                Xem đơn hàng
              </Link>
            </div>
          </div>
        ))}
      </div>
      <br />
      <br />
      <br />
      <br />
      <br />
      <br />
      <Footer />
    </>
  );
}

export default PaymentResult;
