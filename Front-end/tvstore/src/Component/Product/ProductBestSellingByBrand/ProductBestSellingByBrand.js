import React from "react";
import { BrowserRouter as Router, Switch, Route, Link } from "react-router-dom";
import "../../Body/Body.css";
import NumberFormat from "react-number-format";

export default function ProductBestSellingByBrand(props) {
  const { products } = props;
  const linkImage = "http://127.0.0.1:8000/images/";
  return (
    <>
    <div id="featured-product">
      <div style={{ position: "relative" }}>
        <h2 className="new-product-title">Deal Hot Trong Tháng</h2>
        <Link className="gearvn-new-products-hot-view-all" to="#">
          Xem tất cả
          <i className="fa fa-chevron-right"></i>
        </Link>
      </div>
      
      <div className="loop-pro">
        <div className="module-products row">
          {products.map((item,index) => (
            <div className="col-sm-4 col-xs-12 padding-none col-fix20" key={index}>
              <div className="product-row">
                <div className="product-row-img">
                
                  <Link to={`/ProductDetail/${item.id}`}>
                    <img
                      src={linkImage + item.AnhDaiDien}
                      className="product-row-thumbnail"
                      alt={item.AnhDaiDien}
                    />
                  </Link>
                  <div className="product-row-price-hover">
                      <div className="product-row-note pull-left">
                        Xem chi tiết
                      </div>
                    <Link
                      to={`/ProductDetail/${item.id}`}
                      className="product-row-btnbuy pull-right"
                    >
                      Đặt hàng
                    </Link>
                  </div>
                </div>

                <h2 className="product-row-name">{item.TenSanPham}</h2>
                <div className="product-row-info">
                  <div className="product-row-price pull-left">
                    <NumberFormat
                      value={item.GiaCu}
                      displayType={"text"}
                      thousandSeparator={true}
                      suffix={" VNĐ"}
                      renderText={(value, props) => (
                        <del {...props}>{value} </del>
                      )}
                    />
                    <br />
                    <span className="product-row-sale">
                      <NumberFormat
                        value={item.GiaKM}
                        displayType={"text"}
                        thousandSeparator={true}
                        suffix={" VNĐ"}
                        renderText={(value, props) => (
                          <div {...props}>{value}</div>
                        )}
                      />
                    </span>
                  </div>
                  <div className="new-product-percent">{item.SoLuong ==0 ?"Hết hàng" :"Còn hàng"}</div>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
    
    </>
    
    
  );
}
